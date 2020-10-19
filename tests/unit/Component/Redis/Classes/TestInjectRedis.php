<?php

namespace Imi\Test\Component\Redis\Classes;

use Imi\Bean\Annotation\Bean;
use Imi\Redis\Annotation\RedisInject;
use Imi\Redis\RedisHandler;
use PHPUnit\Framework\Assert;

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
