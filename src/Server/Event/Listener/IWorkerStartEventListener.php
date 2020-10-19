<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\WorkerStartEventParam;

/**
 * 监听服务器workerstart事件接口.
 */
interface IWorkerStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param WorkerStartEventParam $e
     *
     * @return void
     */
    public function handle(WorkerStartEventParam $e);
}
