<?php

namespace Imi\Tool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Tool\Tool;

/**
 * @Listener(eventName="IMI.INITED")
 */
class Run implements IEventListener
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
        Tool::run();
    }
}
