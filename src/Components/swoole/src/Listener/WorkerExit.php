<?php

declare(strict_types=1);

namespace Imi\Swoole\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Swoole\Server\Event\Listener\IWorkerExitEventListener;
use Imi\Swoole\Server\Event\Param\WorkerExitEventParam;
use Imi\Util\ImiPriority;
use Swoole\Timer;

/**
 * @Listener(eventName="IMI.MAIN_SERVER.WORKER.EXIT", priority=ImiPriority::IMI_MIN)
 */
class WorkerExit implements IWorkerExitEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(WorkerExitEventParam $e): void
    {
        Timer::clearAll();
    }
}
