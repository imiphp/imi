<?php
namespace Imi\HotUpdate;

use Imi\App;
use Imi\Util\Imi;
use Imi\Util\Coroutine;
use Imi\Bean\BeanFactory;
use Imi\Process\BaseProcess;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Process\Annotation\Process;

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

    public function run(\Swoole\Process $process)
    {
        \Swoole\Runtime::enableCoroutine(false);
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
        go(function(){
            echo 'Process [hotUpdate] start', PHP_EOL;
            $monitor = BeanFactory::newInstance($this->monitorClass, array_merge($this->defaultPath, $this->includePaths), $this->excludePaths);
            $time = 0;
            $this->initBuildRuntime();
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
                    $beginTime = microtime(true);
                    $result = $this->beginBuildRuntime($changedFiles);
                    $this->initBuildRuntime();
                    if("Build app runtime complete" !== trim($result))
                    {
                        echo $result, PHP_EOL, 'Build runtime failed!', PHP_EOL;
                        continue;
                    }
                    // 清除各种缓存
                    $this->clearCache();
                    echo 'Build time use: ', microtime(true) - $beginTime, ' sec', PHP_EOL;
                    // 执行重新加载
                    echo 'Reloading server...', PHP_EOL;
                    $reloadResult = Imi::reloadServer();
                }
            }
        });
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
        $writeContent = "y\n";
        if(strlen($writeContent) !== fwrite($this->buildRuntimePipes[0], $writeContent))
        {
            throw new \RuntimeException('Send to buildRuntime process failed');
        }
        $content = '';
        while($tmp = fgets($this->buildRuntimePipes[1]))
        {
            $content = $tmp;
        }
        return json_decode($content, true);
    }

    /**
     * 关闭 runtime 进程
     *
     * @return void
     */
    private function closeBuildRuntime()
    {
        if(null !== $this->buildRuntimePipes)
        {
            foreach($this->buildRuntimePipes as $pipe)
            {
                fclose($pipe);
            }
            $this->buildRuntimePipes = null;
        }
        if(null !== $this->buildRuntimeHandler)
        {
            proc_close($this->buildRuntimeHandler);
        }
    }
}