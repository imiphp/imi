<?php
namespace Imi\Listener;

use Imi\Process\ProcessManager;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\WorkerStopEventParam;
use Imi\Server\Event\Listener\IWorkerStopEventListener;
use Imi\App;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.STOP",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class OnWorkerStop implements IWorkerStopEventListener
{
    /**
     * 事件处理方法
     * @param WorkerStopEventParam $e
     * @return void
     */
    public function handle(WorkerStopEventParam $e)
    {
        App::getBean('Logger')->save();
    }
}