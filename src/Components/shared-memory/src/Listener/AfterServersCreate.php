<?php

declare(strict_types=1);

namespace Imi\SharedMemory\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;

#[Listener(eventName: 'IMI.SERVERS.CREATE.AFTER', one: true)]
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
