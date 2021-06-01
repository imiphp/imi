<?php

declare(strict_types=1);

namespace Imi\SharedMemory\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER")
 */
class AfterServersCreate implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        ProcessManager::runWithManager('sharedMemory');
    }
}
