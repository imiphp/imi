<?php
namespace Imi\HotUpdate;

use Imi\App;
use Imi\Util\Imi;
use Imi\Util\Coroutine;
use Imi\Bean\BeanFactory;
use Imi\Process\BaseProcess;
use Imi\Bean\Annotation\Bean;
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

    public function run(\Swoole\Process $process)
    {
        if(!$this->status)
        {
            return;
        }
        if(null === $this->defaultPath)
        {
            $this->defaultPath = [
                Imi::getNamespacePath(App::getNamespace()),
            ];
        }
        go(function(){
            echo 'Process [hotUpdate] start', PHP_EOL;
            $monitor = BeanFactory::newInstance($this->monitorClass, array_merge($this->defaultPath, $this->includePaths), $this->excludePaths);
            
            $reloadCmd = Imi::getImiCmd('server', 'reload');
            $time = 0;
            while(true)
            {
                // 检测间隔延时
                sleep(min(max($this->timespan - (microtime(true) - $time), $this->timespan), $this->timespan));
                $time = microtime(true);
                // 检查文件是否有修改
                if($monitor->isChanged())
                {
                    echo 'Prepare reloading...', PHP_EOL;
                    while(true)
                    {
                        $result = exec(Imi::getImiCmd('imi', 'buildRuntime'));
                        $result = json_decode($result);
                        if(true === $result)
                        {
                            break;
                        }
                        else
                        {
                            echo $result, PHP_EOL;
                            sleep(1);
                        }
                    }
                    // 清除各种缓存
                    $this->clearCache();
                    // 执行重新加载
                    Coroutine::exec($reloadCmd);
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
}