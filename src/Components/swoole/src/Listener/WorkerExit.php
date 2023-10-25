<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IWorkerExitEventListener;
use Imi\Swoole\Server\Event\Param\WorkerExitEventParam;
use Imi\Timer\Timer;

#[Listener(eventName: 'IMI.MAIN_SERVER.WORKER.EXIT', priority: -19940312)]
class WorkerExit implements IWorkerExitEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(WorkerExitEventParam $e): void
    {
        Timer::clear();
    }
}
