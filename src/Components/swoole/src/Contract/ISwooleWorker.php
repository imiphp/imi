<?php

declare(strict_types=1);

namespace Imi\Swoole\Contract;

use Imi\Contract\IWorker;

interface ISwooleWorker extends IWorker
{
    /**
     * 获取 task 进程数量.
     */
    public function getTaskWorkerNum(): int;

    /**
     * 是否为 task 进程.
     */
    public function isTask(): bool;

    /**
     * 是否 IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     */
    public function isWorkerStartAppComplete(): bool;

    /**
     * 获取服务器 manager 进程 PID.
     */
    public function getManagerPid(): int;

    /**
     * 返回 workerId 是否是用户进程.
     */
    public function isWorkerIdProcess(int $workerId): bool;
}
