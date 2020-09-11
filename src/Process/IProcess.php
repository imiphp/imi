<?php

namespace Imi\Process;

/**
 * 进程实现接口.
 */
interface IProcess
{
    public function run(\Swoole\Process $process);
}
