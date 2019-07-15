<?php
namespace Imi\Test\Component\Redis\Classes;

use Imi\Redis\RedisHandler;
use Imi\Bean\Annotation\Bean;
use PHPUnit\Framework\Assert;
use Imi\Redis\Annotation\RedisInject;

/**
 * @Bean("TestInjectRedis")
 */
class TestInjectRedis
{
    /**
     * @RedisInject
     *
     * @var \RedisHandler
     */
    protected $redis;

    public function test()
    {
        Assert::assertInstanceOf(RedisHandler::class, $this->redis);
        $time = time();
        $this->redis->set('imi:test:a', $time);
        Assert::assertEquals($time, $this->redis->get('imi:test:a'));
    }
}