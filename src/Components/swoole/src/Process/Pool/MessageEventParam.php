<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Swoole\Process\Pool;
use Swoole\Process;

class MessageEventParam extends WorkerEventParam
{
    public function __construct(
        /**
         * 进程池对象
         */
        ?Pool $pool = null,
        /**
         * 工作进程.
         */
        ?Process $worker = null,
        /**
         * 工作进程ID.
         */
        int $workerId = 0,
        /**
         * 数据.
         */
        public readonly array $data = []
    ) {
        parent::__construct('message', $pool, $worker, $workerId);
    }

    /**
     * Get 数据.
     */
    public function getData(): array
    {
        return $this->data;
    }
}
