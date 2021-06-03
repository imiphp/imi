<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

/**
 * @testdox Lock Annotation
 */
class LockAnnotationTest extends BaseTest
{
    public function test(): void
    {
        $test = App::getBean('TestLockAnnotation');
        $time = microtime(true);
        $throwables = [];
        $channel = new \Swoole\Coroutine\Channel(3);
        try
        {
            for ($i = 0; $i < 3; ++$i)
            {
                $throwables[] = null;
                $index = $i;
                go(function () use (&$throwables, $index, $test, $channel) {
                    try
                    {
                        $test->test();
                    }
                    catch (\Throwable $th)
                    {
                        $throwables[$index] = $th;
                    }
                    finally
                    {
                        $channel->push(1);
                    }
                });
            }
            $count = 0;
            while ($ret = $channel->pop())
            {
                if (1 === $ret)
                {
                    ++$count;
                    if ($count >= 3)
                    {
                        break;
                    }
                }
            }
            $useTime = microtime(true) - $time;
            foreach ($throwables as $th)
            {
                if ($th)
                {
                    throw $th;
                }
            }
        }
        finally
        {
            $channel->close();
        }
        Assert::assertGreaterThan(0.3, $useTime);
    }

    public function testAfterLock(): void
    {
        $test = App::getBean('TestLockAnnotation');
        Assert::assertEquals(2, $test->index());
        Assert::assertEquals(3, $test->index2());
    }
}
