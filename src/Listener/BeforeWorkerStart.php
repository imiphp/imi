<?php
namespace Imi\Listener;

use Imi\App;
use Imi\Util\Imi;
use Imi\Main\Helper;
use Imi\Bean\Annotation\Listener;
use Imi\Util\Process\ProcessType;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Util\Process\ProcessAppContexts;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START",priority=Imi\Util\ImiPriority::IMI_MAX)
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
            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::TASK_WORKER, true);
            Imi::setProcessName('taskWorker');
        }
        else
        {
            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::WORKER, true);
            // swoole 4.1.0 一键协程化
            if(method_exists('\Swoole\Runtime', 'enableCoroutine') && (Helper::getMain(App::getNamespace())->getConfig()['enableCoroutine'] ?? true))
            {
                \Swoole\Runtime::enableCoroutine(true);
            }
            Imi::setProcessName('worker');
        }

        // 初始化 worker
        App::initWorker();
    }
}