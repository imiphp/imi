<?php

declare(strict_types=1);

namespace Imi\Swoole\Model\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Util\MemoryTableManager;

#[Listener(eventName: SwooleEvents::BEFORE_SERVER_START, one: true)]
class Init implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        MemoryTableManager::init();
    }
}
