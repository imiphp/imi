<?php

declare(strict_types=1);

namespace Imi\Swoole\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Core\CoreEvents;
use Imi\Event\IEventListener;
use Imi\Pool\PoolManager;
use Imi\Process\Event\ProcessEvents;
use Imi\Swoole\Event\SwooleEvents;

#[Listener(eventName: SwooleEvents::SERVER_WORKER_EXIT, priority: \Imi\Util\ImiPriority::IMI_MIN)]
#[Listener(eventName: ProcessEvents::PROCESS_END, priority: -19940311)]
#[Listener(eventName: CoreEvents::AFTER_QUICK_START, priority: \Imi\Util\ImiPriority::IMI_MIN)]
class WorkerExit implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        foreach (PoolManager::getNames() as $name)
        {
            PoolManager::getInstance($name)->close();
        }
    }
}
