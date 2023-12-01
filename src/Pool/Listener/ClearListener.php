<?php

declare(strict_types=1);

namespace Imi\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Cli\Event\CommandEvents;
use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;

#[Listener(eventName: CommandEvents::AFTER_COMMAND)]
class ClearListener implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        PoolManager::clearPools();
    }
}
