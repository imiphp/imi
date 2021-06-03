<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\EventParam;
use Imi\Swoole\Process\Pool;
use Swoole\Process;

class WorkerEventParam extends EventParam
{
    /**
     * 进程池对象
     */
    protected Pool $pool;

    /**
     * 工作进程.
     */
    protected Process $worker;

    /**
     * 工作进程ID.
     */
    protected int $workerId = 0;

    /**
     * Get 进程池对象
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }

    /**
     * Get 工作进程.
     */
    public function getWorker(): Process
    {
        return $this->worker;
    }

    /**
     * Get 工作进程ID.
     */
    public function getWorkerId(): int
    {
        return $this->workerId;
    }
}
