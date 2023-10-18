<?php

declare(strict_types=1);

namespace AMQPApp\AMQP\Test;

use Imi\AMQP\Annotation\Consumer;
use Imi\AMQP\Base\BaseConsumer;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Enum\ConsumerResult;
use Imi\Bean\Annotation\Bean;
use Imi\Redis\Redis;

/**
 * 使用连接池中的连接消费.
 *
 * @Bean("TestConsumer")
 *
 * @Consumer(tag="tag-imi", queue="queue-imi-2", message=\AMQPApp\AMQP\Test\TestMessage::class)
 */
class TestConsumer extends BaseConsumer
{
    /**
     * 消费任务
     *
     * @param \AMQPApp\AMQP\Test\TestMessage $message
     *
     * @return mixed
     */
    protected function consume(IMessage $message)
    {
        var_dump(__CLASS__, $message->getBody(), \get_class($message));
        Redis::set('imi-amqp:consume:1:' . $message->getMemberId(), $message->getBody());

        return ConsumerResult::ACK;
    }
}
