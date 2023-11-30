<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\CommonEvent;
use Swoole\Process\Pool;

class ProcessPoolProcessBegin extends CommonEvent
{
    public function __construct(
        public readonly string $name,
        public readonly Pool $pool,
        public readonly int $workerId,
        public readonly int $workerNum,
        public readonly array $args,
        public readonly int $ipcType,
        public readonly string $msgQueueKey
    ) {
        parent::__construct('IMI.PROCESS_POOL.PROCESS.BEGIN');
    }
}
