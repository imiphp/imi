<?php

declare(strict_types=1);

namespace Imi\SharedMemory\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Server\Event\ServerEvents;
use Imi\Swoole\Process\ProcessManager;

#[Listener(eventName: ServerEvents::AFTER_CREATE_SERVERS, one: true)]
class AfterServersCreate implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        ProcessManager::runWithManager('sharedMemory');
    }
}
