<?php

declare(strict_types=1);

use Swoole\Coroutine;

if (\extension_loaded('swoole'))
{
    if (!\function_exists('\go'))
    {
        /**
         * @param mixed ...$params
         *
         * @return int|false
         */
        function go(callable $func, ...$params)
        {
            return Coroutine::create($func, ...$params);
        }
    }

    if (!\function_exists('\defer'))
    {
        function defer(callable $callback): void
        {
            Coroutine::defer($callback);
        }
    }
}
