<?php

declare(strict_types=1);

namespace Imi\Contract;

interface IWorker
{
    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     *
     * @return int|null
     */
    public function getWorkerId(): ?int;

    /**
     * 获取 Worker 进程数量.
     *
     * @return int
     */
    public function getWorkerNum(): int;

    /**
     * 是否初始化完毕.
     *
     * @return bool
     */
    public function isInited(): bool;

    /**
     * 初始化完毕.
     *
     * @return void
     */
    public function inited();

    /**
     * 获取服务器 master 进程 PID.
     *
     * @return int
     */
    public function getMasterPid(): int;
}
