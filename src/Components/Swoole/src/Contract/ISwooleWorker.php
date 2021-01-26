<?php

declare(strict_types=1);

namespace Imi\Swoole\Contract;

use Imi\Contract\IWorker;

interface ISwooleWorker extends IWorker
{
    /**
     * 获取 task 进程数量.
     *
     * @return int
     */
    public function getTaskWorkerNum(): int;

    /**
     * 是否为 task 进程.
     *
     * @return bool
     */
    public function isTask(): bool;

    /**
     * 是否 IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     *
     * @return bool
     */
    public function isWorkerStartAppComplete(): bool;

    /**
     * 获取服务器 manager 进程 PID.
     *
     * @return int
     */
    public function getManagerPid(): int;
}
