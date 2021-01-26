<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\Worker;

class SwooleWorker extends Worker
{
    /**
     * 是否为 task 进程.
     *
     * @return bool
     */
    public static function isTask(): bool
    {
        return static::$workerHandler ? static::$workerHandler->isTask() : false;
    }

    /**
     * 获取 task 进程数量.
     *
     * @return int
     */
    public static function getTaskWorkerNum(): int
    {
        return static::$workerHandler ? static::$workerHandler->getTaskWorkerNum() : 0;
    }

    /**
     * 是否 IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     *
     * @return bool
     */
    public static function isWorkerStartAppComplete(): bool
    {
        return static::$workerHandler ? static::$workerHandler->isWorkerStartAppComplete() : false;
    }

    /**
     * 获取服务器 manager 进程 PID.
     *
     * @return int
     */
    public static function getManagerPid(): int
    {
        return static::$workerHandler ? static::$workerHandler->getManagerPid() : false;
    }
}
