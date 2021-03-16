<?php

declare(strict_types=1);

namespace Imi\Contract;

interface IWorker
{
    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     */
    public function getWorkerId(): ?int;

    /**
     * 获取 Worker 进程数量.
     */
    public function getWorkerNum(): int;

    /**
     * 是否初始化完毕.
     */
    public function isInited(): bool;

    /**
     * 初始化完毕.
     */
    public function inited(): void;

    /**
     * 获取服务器 master 进程 PID.
     */
    public function getMasterPid(): int;
}
