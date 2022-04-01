<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

trait TCoroutine
{
    private function __construct()
    {
    }

    /**
     * 判断当前是否在协程中运行.
     */
    public static function isIn(): bool
    {
        return static::getuid() > -1;
    }

    /**
     * 创建一个新的协程，并立即执行。
     *
     * @param mixed ...$params
     *
     * @return int|bool
     */
    public static function create(callable $callable, ...$params)
    {
        return imigo($callable, ...$params);
    }
}

// @phpstan-ignore-next-line
if (\SWOOLE_VERSION_ID >= 50000)
{
    include __DIR__ . '/Coroutine.typed.php';
}
else
{
    class Coroutine extends \Swoole\Coroutine
    {
        use TCoroutine;
    }
}
