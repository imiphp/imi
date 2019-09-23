<?php
namespace Imi\Db\Listener;

use Imi\Event\Event;
use Imi\Util\ImiPriority;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Event\Param\WorkerStartEventParam;
use Imi\Server\Event\Listener\IWorkerStartEventListener;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.START", priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class WorkerStart implements IWorkerStartEventListener
{
    /**
     * 事件处理方法
     * @param WorkerStartEventParam $e
     * @return void
     */
    public function handle(WorkerStartEventParam $e)
    {
        Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Db\Listener\RequestContextDestroy, 'handle'], ImiPriority::IMI_MIN - 20);
        Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Pool\Listener\RequestContextDestroy, 'handle'], ImiPriority::IMI_MIN - 30);
    }

}
