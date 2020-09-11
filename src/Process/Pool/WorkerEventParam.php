<?php

namespace Imi\Process\Pool;

use Imi\Event\EventParam;

class WorkerEventParam extends EventParam
{
    /**
     * 进程池对象
     *
     * @var \Imi\Process\Pool
     */
    protected $pool;

    /**
     * 工作进程.
     *
     * @var \Imi\Process\Process
     */
    protected $worker;

    /**
     * 工作进程ID.
     *
     * @var int
     */
    protected $workerId;

    /**
     * Get 进程池对象
     *
     * @return \Imi\Process\Pool
     */
    public function getPool()
    {
        return $this->pool;
    }

    /**
     * Get 工作进程.
     *
     * @return \Imi\Process\Process
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * Get 工作进程ID.
     *
     * @return int
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }
}
