<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

/**
 * @testdox Cache Annotation
 */
class CacheAnnotationTest extends BaseTest
{
    /**
     * @testdox Cacheable TTL
     *
     * @return void
     */
    public function testCacheableTTL()
    {
        $test = App::getBean('TestCacheAnnotation');
        $id = 1;
        $result = $test->testCacheableTTL($id);

        $result2 = $test->testCacheableTTL($id);

        Assert::assertTrue(isset($result['id']));
        Assert::assertTrue(isset($result['time']));
        Assert::assertEquals($id, $result['id']);

        Assert::assertTrue(isset($result2['id']));
        Assert::assertTrue(isset($result2['time']));
        Assert::assertEquals($result['time'], $result2['time']);

        sleep(1);

        $result2 = $test->testCacheableTTL($id);
        Assert::assertTrue(isset($result2['id']));
        Assert::assertTrue(isset($result2['time']));
        Assert::assertNotEquals($result['time'], $result2['time']);
    }

    public function testCacheableLock()
    {
        $test = App::getBean('TestCacheAnnotation');
        $id = 1;
        $result = $test->testCacheableLock($id);
        Assert::assertTrue(isset($result['id']));
        Assert::assertTrue(isset($result['time']));
        Assert::assertEquals($id, $result['id']);

        $result2 = $test->testCacheableLock($id);
        Assert::assertTrue(isset($result2['id']));
        Assert::assertTrue(isset($result2['time']));
        Assert::assertEquals($result['time'], $result2['time']);

        sleep(1);

        $result2 = $test->testCacheableLock($id);
        Assert::assertTrue(isset($result2['id']));
        Assert::assertTrue(isset($result2['time']));
        Assert::assertNotEquals($result['time'], $result2['time']);

        $time = microtime(true);
        $throwables = [];
        $channel = new \Swoole\Coroutine\Channel(3);
        for ($i = 0; $i < 3; ++$i)
        {
            $throwables[] = null;
            $index = $i;
            go(function () use (&$throwables, $index, $test, $id, $channel) {
                try
                {
                    $result2 = $test->testCacheableLock($id);
                    Assert::assertTrue(isset($result2['id']));
                    Assert::assertTrue(isset($result2['time']));
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
        $channel->close();
        Assert::assertLessThanOrEqual(1, $useTime);
    }

    public function testCacheEvict()
    {
        $test = App::getBean('TestCacheAnnotation');
        $id = 2;
        $result = $test->testCacheable($id);
        Assert::assertTrue(isset($result['id']));
        Assert::assertTrue(isset($result['time']));
        Assert::assertEquals($id, $result['id']);

        Assert::assertTrue($test->testCacheEvict($id));

        $result2 = $test->testCacheable($id);
        Assert::assertNotEquals($result2, $result);
    }

    public function testCachePut()
    {
        $test = App::getBean('TestCacheAnnotation');
        $id = 3;
        $result = $test->testCachePut($id);
        Assert::assertEquals($id, $result);

        $result2 = $test->testCacheable($id);
        Assert::assertEquals($result, $result2);
    }
}
