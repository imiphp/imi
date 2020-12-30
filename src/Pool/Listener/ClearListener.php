<?php

declare(strict_types=1);

namespace Imi\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;

/**
 * @Listener(eventName="IMI.COMMAND.AFTER")
 */
class ClearListener implements IEventListener
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
        PoolManager::clearPools();
    }
}
