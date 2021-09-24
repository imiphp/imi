<?php

namespace Imi\HotUpdate;

use Imi\Aop\Annotation\Inject;
use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Event\Event;
use Imi\Pool\Annotation\PoolClean;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;
use Imi\Util\Imi;
use Swoole\Timer;

/**
 * @Bean("hotUpdate")
 * @Process(name="hotUpdate", unique=true)
 */
class HotUpdateProcess extends BaseProcess
{
    /**
     * 监视器类.
     *
     * @var string
     */
    protected $monitorClass = \Imi\HotUpdate\Monitor\FileMTime::class;

    /**
     * 每次检测时间间隔，单位：秒（有可能真实时间会大于设定的时间）.
     *
     * @var int
     */
    protected $timespan = 1;

    /**
     * 包含的路径.
     *
     * @var array
     */
    protected $includePaths = [];

    /**
     * 排除的路径.
     *
     * @var array
     */
    protected $excludePaths = [];

    /**
     * 默认监视路径.
     *
     * @var array
     */
    protected $defaultPath = null;

    /**
     * 是否开启热更新，默认开启.
     *
     * @var bool
     */
    protected $status = true;

    /**
     * 热更新检测，更改的文件列表，储存在的文件名.
     *
     * @var string
     */
    protected $changedFilesFile;

    /**
     * buildRuntime resource.
     *
     * @var resource|false|null
     */
    private $buildRuntimeHandler = null;

    /**
     * buildRuntime pipes.
     *
     * @var array|null
     */
    private $buildRuntimePipes = null;

    /**
     * @Inject("ErrorLog")
     *
     * @var \Imi\Log\ErrorLog
     */
    protected $errorLog;

    /**
     * 开始时间.
     *
     * @var float
     */
    private $beginTime;

    /**
     * 是否正在构建中.
     *
     * @var bool
     */
    private $building = false;

    /**
     * 构建运行时计时器ID.
     *
     * @var int
     */
    private $buildRuntimeTimerId;

    /**
     * @PoolClean
     *
     * @param \Swoole\Process $process
     *
     * @return void
     */
    public function run(\Swoole\Process $process)
    {
        \Swoole\Runtime::enableCoroutine(true);
        if (!$this->status)
        {
            return;
        }
        $this->changedFilesFile = Imi::getRuntimePath('changedFilesFile');
        file_put_contents($this->changedFilesFile, '');
        if (null === $this->defaultPath)
        {
            $this->defaultPath = Imi::getNamespacePaths(App::getNamespace());
        }
        $this->excludePaths[] = Imi::getRuntimePath();
        echo 'Process [hotUpdate] start', \PHP_EOL;
        $monitor = BeanFactory::newInstance($this->monitorClass, array_merge($this->defaultPath, $this->includePaths), $this->excludePaths);
        $time = 0;
        $this->initBuildRuntime();
        $this->startBuildRuntimeTimer();
        while (true)
        {
            // 检测间隔延时
            if ($this->timespan > 0)
            {
                $time = $this->timespan - (microtime(true) - $time);
                if ($time <= 0)
                {
                    $time = 10000;
                }
                else
                {
                    $time *= 1000000;
                }
            }
            else
            {
                $time = 10000;
            }
            usleep((int) $time);
            $time = microtime(true);
            // 检查文件是否有修改
            if ($monitor->isChanged())
            {
                $changedFiles = $monitor->getChangedFiles();
                echo 'Found ', \count($changedFiles) , ' changed Files:', \PHP_EOL, implode(\PHP_EOL, $changedFiles), \PHP_EOL;
                file_put_contents($this->changedFilesFile, implode("\n", $changedFiles));
                echo 'Building runtime...', \PHP_EOL;
                if ($this->building)
                {
                    $this->stopBuildRuntimeTimer();
                    $this->initBuildRuntime();
                    $this->startBuildRuntimeTimer();
                }
                $this->beginBuildRuntime($changedFiles);
            }
        }
    }

    /**
     * 开始构建运行时计时器.
     *
     * @return void
     */
    private function startBuildRuntimeTimer()
    {
        $this->buildRuntimeTimerId = Timer::tick(1000, [$this, 'buildRuntimeTimer']);
    }

    /**
     * 停止构建运行时计时器.
     *
     * @return void
     */
    private function stopBuildRuntimeTimer()
    {
        Timer::clear($this->buildRuntimeTimerId);
    }

    /**
     * 清除各种缓存.
     *
     * @param array $changedFiles
     *
     * @return void
     */
    private function clearCache(array $changedFiles)
    {
        static $functions = [
            'apcu_clear_cache',
            'opcache_reset',
        ];
        foreach ($functions as $function)
        {
            if (\function_exists($function))
            {
                $function();
            }
        }
        if (\function_exists('opcache_invalidate'))
        {
            foreach ($changedFiles as $file)
            {
                opcache_invalidate($file, true);
            }
        }
    }

    /**
     * 初始化 runtime.
     *
     * @return void
     */
    private function initBuildRuntime()
    {
        $this->closeBuildRuntime();
        $cmd = Imi::getImiCmd('imi', 'buildRuntime', [
            'format'            => 'json',
            'changedFilesFile'  => $this->changedFilesFile,
            'imi-runtime'       => Imi::getRuntimePath('imi-runtime-bak.cache'),
            'confirm'           => true,
        ]);
        static $descriptorspec = [
            ['pipe', 'r'],  // 标准输入，子进程从此管道中读取数据
            ['pipe', 'w'],  // 标准输出，子进程向此管道中写入数据
        ];
        $this->buildRuntimeHandler = proc_open(cmd($cmd), $descriptorspec, $this->buildRuntimePipes);
        if (false === $this->buildRuntimeHandler)
        {
            throw new \RuntimeException(sprintf('Open "%s" failed', $cmd));
        }
    }

    /**
     * 开始构建 runtime.
     *
     * @param string[] $changedFiles
     *
     * @return void
     */
    private function beginBuildRuntime($changedFiles)
    {
        $this->beginTime = microtime(true);
        $result = null;
        Event::trigger('IMI.HOTUPDATE.BEGIN_BUILD', [
            'changedFiles'      => $changedFiles,
            'changedFilesFile'  => $this->changedFilesFile,
            'result'            => &$result,
        ]);
        // @phpstan-ignore-next-line
        if ($result)
        {
            return;
        }
        $this->building = true;
        try
        {
            if ($this->buildRuntimeHandler)
            {
                $status = proc_get_status($this->buildRuntimeHandler);
                // @phpstan-ignore-next-line
                if (!$status || !($status['running'] ?? false))
                {
                    $this->initBuildRuntime();
                }
            }
            else
            {
                $this->initBuildRuntime();
            }
            $writeContent = "y\n";
            if (\strlen($writeContent) !== fwrite($this->buildRuntimePipes[0], $writeContent))
            {
                throw new \RuntimeException('Send to buildRuntime process failed');
            }
            $content = '';
            while ($tmp = fgets($this->buildRuntimePipes[1]))
            {
                $content = $tmp;
            }
            $result = json_decode($content, true);
            if ('Build app runtime complete' !== trim($result))
            {
                echo $result, \PHP_EOL, 'Build runtime failed!', \PHP_EOL;

                return;
            }
            // 清除各种缓存
            $this->clearCache($changedFiles);
            echo 'Build time use: ', microtime(true) - $this->beginTime, ' sec', \PHP_EOL;
            // 执行重新加载
            echo 'Reloading server...', \PHP_EOL;
            Imi::reloadServer();
        }
        catch (\Throwable $th)
        {
            throw $th;
        }
        finally
        {
            $this->building = false;
        }
    }

    /**
     * 关闭 runtime 进程.
     *
     * @return void
     */
    private function closeBuildRuntime()
    {
        $closePipes = function ($buildRuntimePipes) {
            if (null !== $buildRuntimePipes)
            {
                foreach ($buildRuntimePipes as $pipe)
                {
                    fclose($pipe);
                }
            }
        };
        if ($this->buildRuntimeHandler)
        {
            $buildRuntimeHandler = $this->buildRuntimeHandler;
            $buildRuntimePipes = $this->buildRuntimePipes;
            $status = proc_get_status($buildRuntimeHandler);
            // @phpstan-ignore-next-line
            if (!$status || $status['running'] ?? false)
            {
                $writeContent = "n\n";
                fwrite($buildRuntimePipes[0], $writeContent);
            }
            $this->buildRuntimeHandler = null;
            $this->buildRuntimePipes = null;
            $closePipes($buildRuntimePipes);
            proc_close($buildRuntimeHandler);
        }
        else
        {
            $closePipes($this->buildRuntimePipes);
            $this->buildRuntimePipes = null;
        }
    }

    /**
     * 定时器，用于监听构建进程.
     *
     * @return void
     */
    public function buildRuntimeTimer()
    {
        if (!$this->buildRuntimeHandler)
        {
            $this->initBuildRuntime();

            return;
        }
        $status = proc_get_status($this->buildRuntimeHandler);
        // @phpstan-ignore-next-line
        if (!$status || !($status['running'] ?? false))
        {
            $this->initBuildRuntime();
        }
    }
}
