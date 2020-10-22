<?php

namespace Imi\Test;

use PHPUnit\Framework\TestCase;
use function Yurun\Swoole\Coroutine\goWait;

if (class_exists(TestCase::class))
{
    abstract class BaseTest extends TestCase
    {
        const PERFORMANCE_COUNT = 10000;

        protected function go($callable, $finally = null)
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

        protected function php($phpFile, $args = '')
        {
            $cmd = \Imi\cmd('"' . \PHP_BINARY . "\" \"{$phpFile}\" {$args}");

            return `{$cmd}`;
        }
    }
}
else
{
    // 当 PHPUnit 未引入时，直接执行命令时的支持
    abstract class BaseTest
    {
    }
}
