<?php

namespace AMQPApp\AMQP\Test2;

use Imi\AMQP\Annotation\Consumer;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BaseConsumer;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Enum\ConsumerResult;
use Imi\Bean\Annotation\Bean;
use Imi\Redis\Redis;

/**
 * 使用连接池中的连接消费.
 *
 * @Bean("TestConsumer2")
 * @Consumer(tag="tag-imi", queue="queue-imi-2", message=\AMQPApp\AMQP\Test2\TestMessage2::class)
 */
class TestConsumer2 extends BaseConsumer
{
    /**
     * 消费任务
     *
     * @param \AMQPApp\AMQP\Test2\TestMessage2 $message
     *
     * @return mixed
     */
    protected function consume(IMessage $message)
    {
        var_dump(__CLASS__, $message->getBody(), \get_class($message));
        Redis::set('imi-amqp:consume:2:' . $message->getMemberId(), $message->getBody());

        return ConsumerResult::ACK;
    }
}
