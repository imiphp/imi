<?php

declare(strict_types=1);

namespace Imi\Swoole\Test;

use function Yurun\Swoole\Coroutine\goWait;

abstract class BaseTest extends \Imi\Test\BaseTest
{
    public const PERFORMANCE_COUNT = 1000;

    protected function go(callable $callable, ?callable $finally = null): void
    {
        $throwable = null;
        goWait(function () use ($callable, &$throwable) {
            try
            {
                $callable();
            }
            catch (\Throwable $th)
            {
                $throwable = $th;
            }
        });
        if ($finally)
        {
            $finally();
        }
        if ($throwable)
        {
            throw $throwable;
        }
    }
}
