<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\Event\Event;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;

class Worker
{
    /**
     * 当前进程的WorkerId.
     *
     * @var int|null
     */
    private static ?int $workerId = null;

    /**
     * 是否初始化完毕.
     *
     * @return bool
     */
    private static bool $isInited = false;

    /**
     * IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     *
     * @var bool
     */
    private static bool $workerStartAppComplete = false;

    /**
     * Worker 进程数量.
     *
     * @var int|null
     */
    private static ?int $workerNum = null;

    /**
     * task 进程数量.
     *
     * @var int|null
     */
    private static ?int $taskWorkerNum = null;

    private function __construct()
    {
    }

    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     *
     * @return int|null
     */
    public static function getWorkerId(): ?int
    {
        if (null === static::$workerId)
        {
            $main = ServerManager::getServer('main', ISwooleServer::class);
            if ($main instanceof \Imi\Swoole\Server\Base)
            {
                $workerId = $main->getSwooleServer()->worker_id;
                if ($workerId > -1)
                {
                    static::$workerId = $workerId;
                }
            }
            elseif ($main instanceof \Imi\Swoole\Server\CoServer)
            {
                static::$workerId = $main->getWorkerId();
            }
        }

        return static::$workerId;
    }

    /**
     * 是否初始化完毕.
     *
     * @return bool
     */
    public static function isInited(): bool
    {
        return static::$isInited;
    }

    /**
     * 初始化完毕.
     *
     * @return void
     */
    public static function inited()
    {
        static::$isInited = true;
        $mainServer = ServerManager::getServer('main', ISwooleServer::class);
        static::workerStartApp($mainServer, $mainServer);
    }

    /**
     * 触发 IMI.MAIN_SERVER.WORKER.START.APP 事件.
     *
     * @param \Imi\Swoole\Server\Base $server
     * @param object                  $target
     *
     * @return void
     */
    public static function workerStartApp(Base $server, object $target)
    {
        if (!static::$workerStartAppComplete)
        {
            // 触发项目的workerstart事件
            Event::trigger('IMI.MAIN_SERVER.WORKER.START.APP', [
                'server'    => $server,
                'workerId'  => static::$workerId,
            ], $target, WorkerStartEventParam::class);
            static::$workerStartAppComplete = true;
        }
    }

    /**
     * 是否 IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     *
     * @return bool
     */
    public static function isWorkerStartAppComplete(): bool
    {
        return static::$workerStartAppComplete;
    }

    /**
     * 是否为 task 进程.
     *
     * @return bool
     */
    public static function isTask(): bool
    {
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);

        return $server->getSwooleServer()->taskworker;
    }

    /**
     * 获取 Worker 进程数量.
     *
     * @return int
     */
    public static function getWorkerNum(): int
    {
        if (!static::$workerNum)
        {
            /** @var ISwooleServer $server */
            $server = ServerManager::getServer('main', ISwooleServer::class);
            static::$workerNum = $server->getSwooleServer()->setting['worker_num'];
        }

        return static::$workerNum;
    }

    /**
     * 获取 task 进程数量.
     *
     * @return int
     */
    public static function getTaskWorkerNum(): int
    {
        if (!static::$taskWorkerNum)
        {
            /** @var ISwooleServer $server */
            $server = ServerManager::getServer('main', ISwooleServer::class);
            static::$taskWorkerNum = $server->getSwooleServer()->setting['task_worker_num'];
        }

        return static::$taskWorkerNum;
    }
}
