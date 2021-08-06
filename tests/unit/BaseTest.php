<?php

declare(strict_types=1);

namespace Imi\Test;

use PHPUnit\Framework\TestCase;

if (class_exists(TestCase::class))
{
    abstract class BaseTest extends TestCase
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
                try
                {
                    $callable();
                }
                catch (\Throwable $th)
                {
                    $throwable = $th;
                }
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

        protected function php(string $phpFile, string $args = ''): string
        {
            $cmd = \Imi\cmd('"' . \PHP_BINARY . "\" \"{$phpFile}\" {$args}");

            return shell_exec("{$cmd}");
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
