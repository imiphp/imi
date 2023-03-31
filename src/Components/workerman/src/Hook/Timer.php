<?php

declare(strict_types=1);

namespace Imi\Workerman\Hook;

class Timer extends \Workerman\Timer
{
    public static function clear(): void
    {
        static::delAll();
        // @phpstan-ignore-next-line
        static::$_event = null;
        static::$_timerId = 0;
    }
}
