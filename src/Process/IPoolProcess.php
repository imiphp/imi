<?php
namespace Imi\Process;

/**
 * 进程池进程实现接口
 */
interface IPoolProcess
{
	public function run(\Swoole\Process\Pool $pool, int $workerId);
}