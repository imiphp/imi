<?php

namespace Imi\SharedMemory\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Process\ProcessManager;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER")
 */
class AfterServersCreate implements IEventListener
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
        ProcessManager::runWithManager('sharedMemory');
    }
}
