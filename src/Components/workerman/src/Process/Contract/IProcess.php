<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Contract;

use Workerman\Worker;

/**
 * 进程实现接口.
 */
interface IProcess
{
    public function run(Worker $worker): void;
}
