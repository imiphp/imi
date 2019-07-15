<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Config;
use Imi\Worker;
use Imi\Util\File;
use Imi\Main\Helper;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation;
use Imi\Pool\PoolConfig;
use Imi\Pool\PoolManager;
use Imi\Cache\CacheManager;
use Imi\Bean\Annotation\Listener;
use Imi\Util\CoroutineChannelManager;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Util\Imi;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START",priority=PHP_INT_MAX)
 */
class BeforeWorkerStart implements IWorkerStartEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        // 随机数播种
        mt_srand();

        // 重新加载项目配置及组件
        foreach(Helper::getAppMains() as $main)
        {
            $main->loadConfig();
            $main->loadComponents();
        }

        if($e->server->getSwooleServer()->taskworker)
        {
            Imi::setProcessName('taskWorker');
        }
        else
        {
            // swoole 4.1.0 一键协程化
            if(method_exists('\Swoole\Runtime', 'enableCoroutine') && (Helper::getMain(App::getNamespace())->getConfig()['enableCoroutine'] ?? true))
            {
                \Swoole\Runtime::enableCoroutine(true);
            }
            Imi::setProcessName('worker');
        }

        $GLOBALS['WORKER_START_END_RESUME_COIDS'] = [];

        // 初始化 worker
        App::initWorker();
    }
}