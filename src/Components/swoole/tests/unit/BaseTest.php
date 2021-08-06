<?php

declare(strict_types=1);

namespace Imi\Swoole\Test;

use function Yurun\Swoole\Coroutine\goWait;

abstract class BaseTest extends \Imi\Test\BaseTest
{
    public const PERFORMANCE_COUNT = 1000;

    protected function go(callable $callable, ?callable $finally = null, int $retry = 0): void
    {
        for ($i = 0; $i <= $retry; ++$i)
        {
            if ($i > 0)
            {
                echo 'retry:', $i, \PHP_EOL;
                sleep(1);
            }
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
            if (!$throwable || !$throwable instanceof \PHPUnit\Framework\SelfDescribing)
            {
                break;
            }
        }
        if (isset($throwable))
        {
            throw $throwable;
        }
    }
}
