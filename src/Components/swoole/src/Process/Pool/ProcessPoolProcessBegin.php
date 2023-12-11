<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Pool;

use Imi\Event\CommonEvent;
use Imi\Swoole\Event\SwooleEvents;
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
        parent::__construct(SwooleEvents::PROCESS_POOL_PROCESS_BEGIN);
    }
}
