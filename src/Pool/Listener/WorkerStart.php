<?php

namespace Imi\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\ImiPriority;

/**
 * @Listener(eventName="IMI.INITED", priority=ImiPriority::IMI_MAX)
 */
class WorkerStart implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        Event::on('IMI.REQUEST_CONTENT.DESTROY', [new \Imi\Pool\Listener\RequestContextDestroy(), 'handle'], ImiPriority::IMI_MIN - 30);
    }
}
