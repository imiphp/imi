<?php
namespace Imi\HotUpdate;

use Imi\App;
use Imi\Util\Imi;
use Imi\Event\Event;
use Imi\Util\Coroutine;
use Imi\Bean\BeanFactory;
use Imi\Process\BaseProcess;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use Swoole\Event as SwooleEvent;
use Imi\Pool\Annotation\PoolClean;
use Imi\Process\Annotation\Process;
use Imi\Util\Process\ProcessAppContexts;
use Swoole\Timer;

/**
 * @Bean("hotUpdate")
 * @Process(name="hotUpdate", unique=true)
 */
class HotUpdateProcess extends BaseProcess
{
    /**
     * 监视器类
     * @var \Imi\HotUpdate\Monitor\BaseMonitor
     */
    protected $monitorClass = \Imi\HotUpdate\Monitor\FileMTime::class;

    /**
     * 每次检测时间间隔，单位：秒（有可能真实时间会大于设定的时间）
     * @var integer
     */
    protected $timespan = 1;

    /**
     * 包含的路径
     * @var array
     */
    protected $includePaths = [];

    /**
     * 排除的路径
     * @var array
     */
    protected $excludePaths = [];

    /**
     * 默认监视路径
     * @var array
     */
    protected $defaultPath = null;

    /**
     * 是否开启热更新，默认开启
     * @var boolean
     */
    protected $status = true;

    /**
     * 热更新检测，更改的文件列表，储存在的文件名
     *
     * @var string
     */
    protected $changedFilesFile;

    /**
     * buildRuntime resource
     *
     * @var \resource
     */
    private $buildRuntimeHandler = null;

    /**
     * buildRuntime pipes
     *
     * @var array
     */
    private $buildRuntimePipes = null;

    /**
     * sock 文件名
     *
     * @var string
     */
    private $sockFile;

    /**
     * @Inject("ErrorLog")
     *
     * @var \Imi\Log\ErrorLog
     */
    protected $errorLog;

    /**
     * 连接集合
     *
     * @var resource[]
     */
    private $conns = [];

    /**
     * 开始时间
     *
     * @var float
     */
    private $beginTime;

    /**
     * @PoolClean
     *
     * @param \Swoole\Process $process
     * @return void
     */
    public function run(\Swoole\Process $process)
    {
        \Swoole\Runtime::enableCoroutine(true);
        if(!$this->status)
        {
            return;
        }
        $this->changedFilesFile = Imi::getRuntimePath('changedFilesFile');
        file_put_contents($this->changedFilesFile, '');
        if(null === $this->defaultPath)
        {
            $this->defaultPath = [
                Imi::getNamespacePath(App::getNamespace()),
            ];
        }
        $this->excludePaths[] = Imi::getRuntimePath();
        $this->startSocketServer();
        echo 'Process [hotUpdate] start', PHP_EOL;
        $monitor = BeanFactory::newInstance($this->monitorClass, array_merge($this->defaultPath, $this->includePaths), $this->excludePaths);
        $time = 0;
        $this->initBuildRuntime();
        Timer::tick(1000, [$this, 'buildRuntimeTimer']);
        while(true)
        {
            // 检测间隔延时
            usleep(min(max($this->timespan - (microtime(true) - $time), $this->timespan), $this->timespan) * 1000000);
            $time = microtime(true);
            // 检查文件是否有修改
            if($monitor->isChanged())
            {
                $changedFiles = $monitor->getChangedFiles();
                echo 'Found ', count($changedFiles) , ' changed Files:', PHP_EOL, implode(PHP_EOL, $changedFiles), PHP_EOL;
                file_put_contents($this->changedFilesFile, implode("\n", $changedFiles));
                echo 'Building runtime...', PHP_EOL;
                $this->beginTime = microtime(true);
                $this->beginBuildRuntime($changedFiles);
            }
        }
    }

    /**
     * 清除各种缓存
     *
     * @return void
     */
    private function clearCache()
    {
        static $functions = [
            'apc_clear_cache',
            'opcache_reset',
        ];
        foreach($functions as $function)
        {
            if(function_exists($function))
            {
                $function();
            }
        }
    }

    /**
     * 初始化 runtime
     *
     * @return void
     */
    private function initBuildRuntime()
    {
        $this->closeBuildRuntime();
        $cmd = Imi::getImiCmd('imi', 'buildRuntime', [
            'format'            =>  'json',
            'changedFilesFile'  =>  $this->changedFilesFile,
            'imi-runtime'       =>  Imi::getRuntimePath('imi-runtime-bak.cache'),
            'confirm'           =>  true,
            'sock'              =>  $this->sockFile,
        ]);
        static $descriptorspec = [
            ['pipe', 'r'],  // 标准输入，子进程从此管道中读取数据
            ['pipe', 'w'],  // 标准输出，子进程向此管道中写入数据
        ];
        $this->buildRuntimeHandler = proc_open($cmd, $descriptorspec, $this->buildRuntimePipes);
        if(false === $this->buildRuntimeHandler)
        {
            throw new \RuntimeException(sprintf('Open "%s" failed', $cmd));
        }
    }

    /**
     * 开始构建 runtime
     *
     * @param string[] $changedFiles
     * @return void
     */
    private function beginBuildRuntime($changedFiles)
    {
        $result = null;
        Event::trigger('IMI.HOTUPDATE.BEGIN_BUILD', [
            'changedFiles'      =>  $changedFiles,
            'changedFilesFile'  =>  $this->changedFilesFile,
            'result'            =>  &$result,
        ]);
        if($result)
        {
            return $result;
        }
        $data = [
            'action'    =>  'buildRuntime',
        ];
        $content = serialize($data);
        $content = pack('N', strlen($content)) . $content;
        foreach($this->conns as $conn)
        {
            fwrite($conn, $content);
        }
    }

    /**
     * 关闭 runtime 进程
     *
     * @return void
     */
    private function closeBuildRuntime()
    {
        $closePipes = function(){
            if(null !== $this->buildRuntimePipes)
            {
                foreach($this->buildRuntimePipes as $pipe)
                {
                    fclose($pipe);
                }
                $this->buildRuntimePipes = null;
            }
        };
        if(null !== $this->buildRuntimeHandler)
        {
            $status = proc_get_status($this->buildRuntimeHandler);
            if($status['running'] ?? false)
            {
                $writeContent = "n\n";
                fwrite($this->buildRuntimePipes[0], $writeContent);
            }
            $closePipes();
            proc_close($this->buildRuntimeHandler);
            $this->buildRuntimeHandler = null;
        }
        else
        {
            $closePipes();
        }
    }

    /**
     * 开始 Unix Socket 服务
     *
     * @return void
     */
    private function startSocketServer()
    {
        imigo(function(){
            $this->sockFile = '/tmp/imi.' . App::get(ProcessAppContexts::MASTER_PID) . '.hotupdate.sock';
            if(is_file($this->sockFile))
            {
                unlink($this->sockFile);
            }
            $this->socket = stream_socket_server('unix://' . $this->sockFile, $errno, $errstr);
            if(false === $this->socket)
            {
                throw new \RuntimeException(sprintf('Create unix socket server failed, errno: %s, errstr: %s, file: %', $errno, $errstr, $this->sockFile));
            }
            while(true)
            {
                $arrRead = [$this->socket];
                $arrWrite = [];
                if(stream_select($arrRead, $arrWrite, $arrWrite, null))
                {
                    $conn = stream_socket_accept($this->socket, 1);
                    if(false === $conn)
                    {
                        continue;
                    }
                    imigo(function() use($conn){
                        $this->parseConn($conn);
                    });
                }
            }
        });
    }

    /**
     * 处理连接
     *
     * @param resource $conn
     * @return void
     */
    private function parseConn($conn)
    {
        $this->conns[(int)$conn] = $conn;
        try {
            stream_set_timeout($conn, 60);
            while(true)
            {
                try {
                    $meta = fread($conn, 4);
                    if('' === $meta)
                    {
                        if(feof($conn))
                        {
                            return;
                        }
                        continue;
                    }
                    if(false === $meta)
                    {
                        return;
                    }
                    $length = unpack('N', $meta)[1];
                    $data = fread($conn, $length);
                    if(false === $data || !isset($data[$length - 1]))
                    {
                        return;
                    }
                    $result = unserialize($data);
                    switch($result['action'] ?? null)
                    {
                        case 'buildRuntimeResult':
                            if("Build app runtime complete" !== trim($result['result']))
                            {
                                echo $result['result'], PHP_EOL, 'Build runtime failed!', PHP_EOL;
                                break;
                            }
                            // 清除各种缓存
                            $this->clearCache();
                            echo 'Build time use: ', microtime(true) - $this->beginTime, ' sec', PHP_EOL;
                            // 执行重新加载
                            echo 'Reloading server...', PHP_EOL;
                            Imi::reloadServer();
                            break;
                    }
                } catch(\Throwable $th) {
                    $this->errorLog->onException($th);
                }
            }
        } finally {
            unset($this->conns[(int)$conn]);
        }
    }

    /**
     * 定时器，用于监听构建进程
     *
     * @return void
     */
    public function buildRuntimeTimer()
    {
        if(!$this->buildRuntimeHandler)
        {
            $this->initBuildRuntime();
            return;
        }
        $status = proc_get_status($this->buildRuntimeHandler);
        if(!($status['running'] ?? false))
        {
            $this->initBuildRuntime();
        }
    }

}