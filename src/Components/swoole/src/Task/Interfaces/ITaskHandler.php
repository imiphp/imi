<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Interfaces;

use Imi\Swoole\Task\TaskParam;

interface ITaskHandler
{
    /**
     * 任务处理方法，返回的值会通过 finish 事件推送给 worker 进程.
     */
    public function handle(TaskParam $param, \Swoole\Server $server, int $taskId, int $workerId): mixed;

    /**
     * 任务结束时触发.
     */
    public function finish(\Swoole\Server $server, int $taskId, mixed $data): void;
}
