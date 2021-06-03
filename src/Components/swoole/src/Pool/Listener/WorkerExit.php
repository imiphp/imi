<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;
use Imi\Util\ImiPriority;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.EXIT", priority=ImiPriority::IMI_MIN)
 * @Listener(eventName="IMI.PROCESS.END", priority=ImiPriority::IMI_MIN)
 */
class WorkerExit implements IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        foreach (PoolManager::getNames() as $name)
        {
            PoolManager::getInstance($name)->close();
        }
    }
}
