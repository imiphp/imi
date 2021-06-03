<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Contract;

/**
 * 进程池进程实现接口.
 */
interface IPoolProcess
{
    /**
     * 进程执行.
     */
    public function run(\Swoole\Process\Pool $pool, int $workerId, string $name, int $workerNum, array $args, int $ipcType, string $msgQueueKey): void;
}
