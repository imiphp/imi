<?php

namespace Imi\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Main\Helper;
use Imi\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Util\Imi;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeWorkerStart implements IWorkerStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        // 随机数播种
        mt_srand();

        // 重新加载项目配置及组件
        foreach (Helper::getAppMains() as $main)
        {
            // $main->loadConfig();
            // $main->loadComponents();
        }

        if ($e->server->getSwooleServer()->taskworker)
        {
            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::TASK_WORKER, true);
            Imi::setProcessName('taskWorker');
        }
        else
        {
            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::WORKER, true);
            // 一键协程化
            if (Config::get('@app.enableCoroutine', true) && Config::get('@app.mainServer.configs.enable_coroutine', true))
            {
                \Swoole\Runtime::enableCoroutine(true);
            }
            Imi::setProcessName('worker');
        }

    }
}
