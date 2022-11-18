<?php

declare(strict_types=1);

use Imi\Log\Log;
use Swoole\Coroutine;

if (!\function_exists('\imigo'))
{
    /**
     * 启动一个协程，自动创建和销毁上下文.
     *
     * @param mixed $args
     */
    function imigo(callable $callable, ...$args): int
    {
        $callable = imiCallable($callable);

        return Coroutine::create(static function () use ($callable, $args): void {
            try
            {
                $callable(...$args);
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        });
    }
}

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
