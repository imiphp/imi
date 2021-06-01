<?php

declare(strict_types=1);

namespace AMQPApp\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Swoole\Process\ProcessManager;

/**
 * @Listener(eventName="IMI.SERVERS.CREATE.AFTER")
 */
class OnServerCreateAfter implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        // 挂载进程到 Manager 进程下
        // ProcessManager::runWithManager('Test');
    }
}
