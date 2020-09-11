<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\WorkerErrorEventParam;

/**
 * 监听服务器workererror事件接口.
 */
interface IWorkerErrorEventListener
{
    /**
     * 事件处理方法.
     *
     * @param WorkerStopEventParam $e
     *
     * @return void
     */
    public function handle(WorkerErrorEventParam $e);
}
