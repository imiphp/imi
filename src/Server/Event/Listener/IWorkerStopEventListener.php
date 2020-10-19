<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\WorkerStopEventParam;

/**
 * 监听服务器workerstop事件接口.
 */
interface IWorkerStopEventListener
{
    /**
     * 事件处理方法.
     *
     * @param WorkerStopEventParam $e
     *
     * @return void
     */
    public function handle(WorkerStopEventParam $e);
}
