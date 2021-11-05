<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\App;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Pool\PoolManager;
use Imi\Redis\Redis;
use Imi\Redis\RedisHandler;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

/**
 * @testdox Redis
 */
class RedisTest extends BaseTest
{
    public function testInject(): void
    {
        /** @var \Imi\Test\Component\Redis\Classes\TestInjectRedis $test */
        $test = App::getBean('TestInjectRedis');
        $test->test();
    }

    public function testSet(): void
    {
        Assert::assertTrue(Redis::set('imi:test:a', 'imi niubi!'));
    }

    public function testGet(): void
    {
        Assert::assertEquals('imi niubi!', Redis::get('imi:test:a'));
    }

    public function testEvalEx(): void
    {
        $value = PoolManager::use('redis_test', function (IPoolResource $resource, RedisHandler $redis) {
            return $redis->evalEx(<<<'SCRIPT'
            local key = KEYS[1]
            local value = ARGV[1]
            redis.call('set', key, value)
            return redis.call('get', key)
            SCRIPT
, ['imi:test:a', 'imi very 6'], 1);
        });
        Assert::assertEquals('imi very 6', $value);
    }

    public function testEvalEx2(): void
    {
        $value = Redis::evalEx(<<<'SCRIPT'
        local key = KEYS[1]
        local value = ARGV[1]
        redis.call('set', key, value)
        return redis.call('get', key)
        SCRIPT
, ['imi:test:a', 'imi very 6'], 1);
        Assert::assertEquals('imi very 6', $value);
    }

    public function testScanEach(): void
    {
        $excepted = $map = [];
        for ($i = 0; $i < 100; ++$i)
        {
            $key = 'imi:scanEach:' . $i;
            $excepted[$key] = 1;
            $map[$key] = 0;
            Redis::set($key, $i);
        }
        foreach (Redis::scanEach('imi:scanEach:*', 10) as $value)
        {
            $map[$value] = 1;
        }
        $this->assertEquals($excepted, $map);
    }

    public function testHscanEach(): void
    {
        $excepted = $map = $values = $exceptedValues = [];
        $key = 'imi:hscanEach';
        Redis::del($key);
        for ($i = 0; $i < 100; ++$i)
        {
            $member = 'value:' . $i;
            $excepted[$member] = 1;
            $map[$member] = 0;
            $values[$member] = -1;
            $exceptedValues[$member] = $i;
            Redis::hSet($key, $member, $i);
        }
        foreach (Redis::hscanEach($key, 'value:*', 10) as $k => $value)
        {
            $map[$k] = 1;
            $values[$k] = $value;
        }
        $this->assertEquals($excepted, $map);
        $this->assertEquals($exceptedValues, $values);
    }

    public function testSscanEach(): void
    {
        $excepted = $map = [];
        $key = 'imi:sscanEach';
        Redis::del($key);
        for ($i = 0; $i < 100; ++$i)
        {
            $value = 'value:' . $i;
            $excepted[$value] = 1;
            $map[$value] = 0;
            Redis::sAdd($key, $value);
        }
        foreach (Redis::sscanEach($key, '*', 10) as $value)
        {
            $map[$value] = 1;
        }
        $this->assertEquals($excepted, $map);
    }

    public function testZscanEach(): void
    {
        $excepted = $map = [];
        $key = 'imi:zscanEach';
        Redis::del($key);
        for ($i = 0; $i < 100; ++$i)
        {
            $value = 'value:' . $i;
            $excepted[$i] = 1;
            $map[$i] = 0;
            Redis::zAdd($key, $i, $value);
        }
        foreach (Redis::zscanEach($key, '*', 10) as $score)
        {
            $map[$score] = 1;
        }
        $this->assertEquals($excepted, $map);
    }
}
