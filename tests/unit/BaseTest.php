<?php

namespace Imi\Test;

use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

if (class_exists(TestCase::class))
{
    abstract class BaseTest extends TestCase
    {
        const PERFORMANCE_COUNT = 1000;

        /**
         * @param callable      $callable
         * @param callable|null $finally
         *
         * @return void
         */
        protected function go($callable, $finally = null)
        {
            $throwable = null;
            $cid = imigo(function () use ($callable, &$throwable) {
                try
                {
                    $callable();
                }
                catch (\Throwable $th)
                {
                    $throwable = $th;
                }
            });
            while (Coroutine::exists($cid))
            {
                Coroutine::sleep(0.01);
            }
            if ($finally)
            {
                $finally();
            }
            if (null !== $throwable)
            {
                throw $throwable;
            }
        }

        /**
         * @param string $phpFile
         * @param string $args
         *
         * @return string
         */
        protected function php($phpFile, $args = '')
        {
            $cmd = cmd('"' . \PHP_BINARY . "\" \"{$phpFile}\" {$args}");

            return `{$cmd}`;
        }

        /**
         * @return void
         */
        public function startTest()
        {
            static $run = false;
            if (!$run)
            {
                $run = true;
                $this->__startTest();
            }
        }

        /**
         * @return void
         */
        public function __startTest()
        {
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
