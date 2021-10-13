<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;

/**
 * 监听服务器workerstart事件接口.
 */
interface IWorkerStartEventListener
{
    public function handle(WorkerStartEventParam $e): void;
}
