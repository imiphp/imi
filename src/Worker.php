<?php
namespace Imi;

abstract class Worker
{
    /**
     * 当前进程的WorkerID
     *
     * @var int
     */
    private static $workerID;

    /**
     * 是否初始化完毕
     *
     * @return boolean
     */
    private static $isInited = false;

    /**
     * 此方法请勿手动调用，避免错乱！
     * 设置当前 worker 进程的 ID
     * 注意，不是进程ID
     *
     * @param int $workerID
     * @return void
     */
    public static function setWorkerID($workerID)
    {
        static::$workerID = $workerID;
    }

    /**
     * 获取当前 worker 进程的 ID
     * 注意，不是进程ID
     *
     * @return int|null
     */
    public static function getWorkerID()
    {
        return static::$workerID;
    }

    /**
     * 是否初始化完毕
     *
     * @return boolean
     */
    public static function isInited()
    {
        return static::$isInited;
    }

    /**
     * 初始化完毕
     *
     * @return void
     */
    public static function inited()
    {
        static::$isInited = true;
    }
}