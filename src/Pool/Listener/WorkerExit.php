<?php

namespace Imi\Pool\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Pool\PoolManager;
use Imi\Server\Event\Listener\IWorkerExitEventListener;
use Imi\Server\Event\Param\WorkerExitEventParam;
use Imi\Util\ImiPriority;

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
        foreach (PoolManager::getNames() as $name)
        {
            PoolManager::getInstance($name)->close();
        }
    }
}
