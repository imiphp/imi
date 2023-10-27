<?php

declare(strict_types=1);

namespace Imi\Swoole\Test;

use function Yurun\Swoole\Coroutine\goWait;

abstract class BaseTestCase extends \Imi\Test\BaseTest
{
    public const PERFORMANCE_COUNT = 1000;

    protected function go(callable $callable, ?callable $finally = null, int $retry = 0): void
    {
        $time = 0;
        for ($i = 0; $i <= $retry; ++$i)
        {
            if ($i > 0)
            {
                $backtrace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? null;
                if ($backtrace)
                {
                    echo $backtrace['class'] , $backtrace['type'] , $backtrace['function'], '()', \PHP_EOL;
                }
                echo 'lastUseTime:', $time, 's', \PHP_EOL;
                echo 'retry:', $i, \PHP_EOL;
                usleep(1000 * random_int(1000, 2000));
            }
            $throwable = null;
            $time = microtime(true);
            goWait(static function () use ($callable, &$throwable): void {
                try
                {
                    $callable();
                }
                catch (\Throwable $th)
                {
                    $throwable = $th;
                }
            });
            $time = microtime(true) - $time;
            if ($finally)
            {
                $finally();
            }
            if (!$throwable || !$throwable instanceof \PHPUnit\Framework\SelfDescribing)
            {
                break;
            }
            if ($i > 0)
            {
                echo 'useTime:', $time, 's', \PHP_EOL;
            }
        }
        if (isset($throwable))
        {
            throw $throwable;
        }
    }
}
