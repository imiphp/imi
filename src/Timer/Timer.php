<?php

declare(strict_types=1);

namespace Imi\Timer;

use Imi\Config;
use Imi\Timer\Contract\ITimer;

class Timer
{
    /**
     * 定时器.
     *
     * @var ITimer
     */
    private static ITimer $timer;

    private function __construct()
    {
    }

    /**
     * 获取定时器实例.
     *
     * @return \Imi\Timer\Contract\ITimer
     */
    public static function getInstance(): ITimer
    {
        if (!isset(static::$timer))
        {
            $timerClass = Config::get('@app.imi.Timer', \Imi\Timer\Handler\DefaultTimer::class);

            return static::$timer = new $timerClass();
        }

        return static::$timer;
    }

    /**
     * 创建一个定时器，无限执行.
     *
     * @param int      $ms
     * @param callable $callback
     *
     * @return int
     */
    public static function tick(int $ms, callable $callback): int
    {
        return static::getInstance()->tick($ms, $callback);
    }

    /**
     * 创建一个延迟执行定时器，只执行一次
     *
     * @param int      $ms
     * @param callable $callback
     *
     * @return int
     */
    public static function after(int $ms, callable $callback): int
    {
        return static::getInstance()->after($ms, $callback);
    }

    /**
     * 删除定时器.
     *
     * @param int $id
     *
     * @return void
     */
    public static function del(int $id): void
    {
        static::getInstance()->del($id);
    }

    /**
     * 清空所有定时器.
     *
     * @return void
     */
    public static function clear(): void
    {
        static::getInstance()->clear();
    }
}
