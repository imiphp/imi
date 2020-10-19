<?php

namespace Imi\Util;

abstract class Coroutine extends \Swoole\Coroutine
{
    /**
     * 判断当前是否在协程中运行.
     *
     * @return bool
     */
    public static function isIn()
    {
        return static::getuid() > -1;
    }

    /**
     * 创建一个新的协程，并立即执行。
     *
     * @param callable $callable
     * @param mixed    ...$params
     *
     * @return int|bool
     */
    public static function create(callable $callable, ...$params)
    {
        return imigo($callable, ...$params);
    }
}
