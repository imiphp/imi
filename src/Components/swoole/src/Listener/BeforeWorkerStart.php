<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IWorkerStartEventListener;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Util\Imi;
use Imi\Util\Imi as ImiUtil;
use Imi\Util\Process\ProcessAppContexts;
use Imi\Util\Process\ProcessType;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START", priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeWorkerStart implements IWorkerStartEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(WorkerStartEventParam $e): void
    {
        // 随机数播种
        mt_srand();

        App::getApp()->loadConfig();

        ImiUtil::loadRuntimeInfo(ImiUtil::getCurrentModeRuntimePath('runtime'));

        if ($e->server->getSwooleServer()->taskworker)
        {
            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::TASK_WORKER, true);
            Imi::setProcessName('taskWorker');
        }
        else
        {
            App::set(ProcessAppContexts::PROCESS_TYPE, ProcessType::WORKER, true);
            Imi::setProcessName('worker');
        }
    }
}
