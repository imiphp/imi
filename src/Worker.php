<?php

namespace Imi;

use Imi\Event\Event;
use Imi\Server\Event\Param\WorkerStartEventParam;

abstract class Worker
{
    /**
     * 当前进程的WorkerID.
     *
     * @var int
     */
    private static $workerID;

    /**
     * 是否初始化完毕.
     *
     * @var bool
     */
    private static $isInited = false;

    /**
     * IMI.MAIN_SERVER.WORKER.START.APP 事件执行完毕.
     *
     * @var bool
     */
    private static $workerStartAppComplete = false;

    /**
     * Worker 进程数量.
     *
     * @var int
     */
    private static $workerNum;

    /**
     * task 进程数量.
     *
     * @var int
     */
    private static $taskWorkerNum;

    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID.
     *
     * @return int|null
     */
    public static function getWorkerID()
    {
        if (null === static::$workerID)
        {
            $main = ServerManage::getServer('main');
            if ($main instanceof \Imi\Server\Base)
            {
                $workerID = $main->getSwooleServer()->worker_id;
                if ($workerID > -1)
                {
                    static::$workerID = $workerID;
                }
            }
            // @phpstan-ignore-next-line
            elseif ($main instanceof \Imi\Server\CoServer)
            {
                static::$workerID = $main->getWorkerId();
            }
        }

        return static::$workerID;
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
        $mainServer = ServerManage::getServer('main');
        static::workerStartApp($mainServer, $mainServer);
    }

    /**
     * 触发 IMI.MAIN_SERVER.WORKER.START.APP 事件.
     *
     * @param \Imi\Server\Base $server
     * @param mixed            $target
     *
     * @return void
     */
    public static function workerStartApp($server, $target)
    {
        if (!static::$workerStartAppComplete)
        {
            // 触发项目的workerstart事件
            Event::trigger('IMI.MAIN_SERVER.WORKER.START.APP', [
                'server'    => $server,
                'workerID'  => static::$workerID,
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
        return ServerManage::getServer('main')->getSwooleServer()->taskworker;
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
            static::$workerNum = ServerManage::getServer('main')->getSwooleServer()->setting['worker_num'];
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
            static::$taskWorkerNum = ServerManage::getServer('main')->getSwooleServer()->setting['task_worker_num'];
        }

        return static::$taskWorkerNum;
    }

    /**
     * 返回 workerId 是否是用户进程.
     *
     * @param int $workerId
     *
     * @return bool
     */
    public static function isWorkerIdProcess(int $workerId): bool
    {
        return $workerId >= self::getWorkerNum() + self::getTaskWorkerNum();
    }
}
