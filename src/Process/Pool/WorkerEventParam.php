<?php

declare(strict_types=1);

namespace Imi\Process\Pool;

use Imi\Event\EventParam;
use Imi\Process\Pool;
use Imi\Process\Process;

class WorkerEventParam extends EventParam
{
    /**
     * 进程池对象
     *
     * @var \Imi\Process\Pool
     */
    protected Pool $pool;

    /**
     * 工作进程.
     *
     * @var \Imi\Process\Process
     */
    protected Process $worker;

    /**
     * 工作进程ID.
     *
     * @var int
     */
    protected int $workerId;

    /**
     * Get 进程池对象
     *
     * @return \Imi\Process\Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }

    /**
     * Get 工作进程.
     *
     * @return \Imi\Process\Process
     */
    public function getWorker(): Process
    {
        return $this->worker;
    }

    /**
     * Get 工作进程ID.
     *
     * @return int
     */
    public function getWorkerId(): int
    {
        return $this->workerId;
    }
}
