<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\WorkerExitEventParam;

/**
 * 监听服务器workexit事件接口.
 */
interface IWorkerExitEventListener
{
    /**
     * 事件处理方法.
     *
     * @param WorkerExitEventParam $e
     *
     * @return void
     */
    public function handle(WorkerExitEventParam $e);
}
