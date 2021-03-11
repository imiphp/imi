<?php

namespace Imi\Process;

/**
 * 进程实现接口.
 */
interface IProcess
{
    /**
     * @param \Swoole\Process $process
     *
     * @return void
     */
    public function run(\Swoole\Process $process);
}
