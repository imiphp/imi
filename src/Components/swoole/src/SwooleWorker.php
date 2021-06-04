<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\Swoole\Contract\ISwooleWorker;
use Imi\Worker;

/**
 * @method static void setWorkerHandler(ISwooleWorker $workerHandler)
 * @method static ISwooleWorker|null getWorkerHandler()
 */
class SwooleWorker extends Worker
{
    /**
     * 是否为 task 进程.
     */
    public static function isTask(): bool
    {
        return static::getWorkerHandler()->isTask();
    }

    /**
     * 获取 task 进程数量.
     */
    public static function getTaskWorkerNum(): int
    {
        return static::getWorkerHandler()->getTaskWorkerNum();
    }

    /**
     * 是否 IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     */
    public static function isWorkerStartAppComplete(): bool
    {
        return static::getWorkerHandler()->isWorkerStartAppComplete();
    }

    /**
     * 获取服务器 manager 进程 PID.
     */
    public static function getManagerPid(): int
    {
        return static::getWorkerHandler()->getManagerPid();
    }

    /**
     * 返回 workerId 是否是用户进程.
     */
    public static function isWorkerIdProcess(int $workerId): bool
    {
        return static::getWorkerHandler()->isWorkerIdProcess($workerId);
    }
}
