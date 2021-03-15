<?php

declare(strict_types=1);

namespace Imi;

use Imi\Contract\IWorker;

class Worker
{
    protected static ?IWorker $workerHandler = null;

    private function __construct()
    {
    }

    public static function setWorkerHandler(IWorker $workerHandler): void
    {
        static::$workerHandler = $workerHandler;
    }

    public static function getWorkerHandler(): IWorker
    {
        if (!static::$workerHandler)
        {
            throw new \RuntimeException('WorkerHandler is null');
        }

        return static::$workerHandler;
    }

    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     *
     * @return int|null
     */
    public static function getWorkerId(): ?int
    {
        return static::$workerHandler ? static::$workerHandler->getWorkerId() : null;
    }

    /**
     * 是否初始化完毕.
     *
     * @return bool
     */
    public static function isInited(): bool
    {
        return static::$workerHandler ? static::$workerHandler->isInited() : false;
    }

    /**
     * 初始化完毕.
     *
     * @return void
     */
    public static function inited(): void
    {
        if (static::$workerHandler)
        {
            static::$workerHandler->inited();
        }
    }

    /**
     * 获取 Worker 进程数量.
     *
     * @return int
     */
    public static function getWorkerNum(): int
    {
        return static::$workerHandler ? static::$workerHandler->getWorkerNum() : 0;
    }

    /**
     * 获取服务器 master 进程 PID.
     *
     * @return int
     */
    public static function getMasterPid(): int
    {
        return static::$workerHandler ? static::$workerHandler->getMasterPid() : 0;
    }
}
