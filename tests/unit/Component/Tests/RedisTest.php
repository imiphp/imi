<?php
namespace Imi\Test\Component\Tests;

use Imi\Test\BaseTest;
use Imi\App;
use Imi\Redis\RedisManager;
use PHPUnit\Framework\Assert;
use Imi\Redis\RedisHandler;
use Imi\Pool\PoolManager;
use Imi\Redis\Redis;

/**
 * @testdox Redis
 */
class RedisTest extends BaseTest
{
    public function testInject()
    {
        $test = App::getBean('TestInjectRedis');
        $test->test();
    }

    public function testSet()
    {
        Assert::assertTrue(Redis::set('imi:test:a', 'imi niubi!'));
    }

    public function testGet()
    {
        Assert::assertEquals('imi niubi!', Redis::get('imi:test:a'));
    }

    public function testEvalEx()
    {
        $value = PoolManager::use('redis_test', function( $resource, RedisHandler $redis){
            return $redis->evalEx(<<<SCRIPT
local key = KEYS[1]
local value = ARGV[1]
redis.call('set', key, value)
return redis.call('get', key)
SCRIPT
, ['imi:test:a', 'imi very 6'], 1);
        });
        Assert::assertEquals('imi very 6', $value);
    }

}