<?php

namespace Imi\Pool\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;

class RequestContextDestroy implements IEventListener
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
        PoolManager::destroyCurrentContext();
    }
}
