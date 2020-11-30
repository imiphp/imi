<?php

declare(strict_types=1);

namespace Imi\Process;

/**
 * 进程实现接口.
 */
interface IProcess
{
    public function run(\Swoole\Process $process);
}
