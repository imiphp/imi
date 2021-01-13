<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\EventParam;
use Imi\Swoole\Process\Pool;
use Imi\Swoole\Process\Process;

class WorkerEventParam extends EventParam
{
    /**
     * 进程池对象
     *
     * @var \Imi\Swoole\Process\Pool
     */
    protected Pool $pool;

    /**
     * 工作进程.
     *
     * @var \Imi\Swoole\Process\Process
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
     * @return \Imi\Swoole\Process\Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }

    /**
     * Get 工作进程.
     *
     * @return \Imi\Swoole\Process\Process
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
