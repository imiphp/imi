<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;

#[Listener(eventName: 'IMI.MAIN_SERVER.WORKER.EXIT', priority: \Imi\Util\ImiPriority::IMI_MIN)]
#[Listener(eventName: 'IMI.PROCESS.END', priority: -19940311)]
#[Listener(eventName: 'IMI.QUICK_START_AFTER', priority: \Imi\Util\ImiPriority::IMI_MIN)]
class WorkerExit implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        foreach (PoolManager::getNames() as $name)
        {
            PoolManager::getInstance($name)->close();
        }
    }
}
