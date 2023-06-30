<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IWorkerExitEventListener;
use Imi\Swoole\Server\Event\Param\WorkerExitEventParam;
use Imi\Timer\Timer;
use Imi\Util\ImiPriority;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.EXIT", priority=ImiPriority::IMI_MIN)
 */
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
