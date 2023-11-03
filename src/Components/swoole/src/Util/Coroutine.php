<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

class Coroutine extends \Swoole\Coroutine
{
    /**
     * 判断当前是否在协程中运行.
     */
    public static function isIn(): bool
    {
        return static::getuid() > -1;
    }

    /**
     * 创建一个新的协程，并立即执行。
     */
    public static function create(callable $callable, mixed ...$params): int|false
    {
        return imigo($callable, ...$params);
    }
}
