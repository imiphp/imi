<?php

declare(strict_types=1);

namespace AMQPApp\AMQP\Test;

use Imi\AMQP\Annotation\Connection;
use Imi\AMQP\Annotation\Consumer;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BaseConsumer;
use Imi\AMQP\Contract\IMessage;
use Imi\AMQP\Enum\ConsumerResult;
use Imi\Bean\Annotation\Bean;
use Imi\Redis\Redis;

/**
 * 启动一个新连接消费.
 *
 * @Bean("TestConsumer")
 * @Connection(host=AMQP_SERVER_HOST, port=5672, user="guest", password="guest")
 * @Consumer(tag="tag-imi", queue="queue-imi-1", message=\AMQPApp\AMQP\Test\TestMessage::class)
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
