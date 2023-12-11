<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\CommonEvent;
use Imi\Swoole\Process\Pool;
use Swoole\Process;

class WorkerEventParam extends CommonEvent
{
    public function __construct(
        string $__eventName,
        /**
         * 进程池对象
         */
        public readonly ?Pool $pool = null,

        /**
         * 工作进程.
         */
        public readonly ?Process $worker = null,

        /**
         * 工作进程ID.
         */
        public readonly int $workerId = 0
    ) {
        parent::__construct($__eventName, $pool);
    }

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
