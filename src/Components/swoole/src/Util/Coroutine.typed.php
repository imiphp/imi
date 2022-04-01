<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

class Coroutine extends \Swoole\Coroutine
{
    use TCoroutine {
        create as private __create;
    }

    /**
     * 创建一个新的协程，并立即执行。
     */
    public static function create(callable $callable, mixed ...$params): int|false
    {
        return self::__create($callable, ...$params);
    }
}
