<?php

declare(strict_types=1);

namespace Imi\Test;

use PHPUnit\Framework\TestCase;

if (class_exists(TestCase::class))
{
    abstract class BaseTest extends TestCase
    {
        const PERFORMANCE_COUNT = 1000;

        protected function go($callable, $finally = null)
        {
            try
            {
                $callable();
            }
            catch (\Throwable $th)
            {
                throw $th;
            }
            finally
            {
                if ($finally)
                {
                    $finally();
                }
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
