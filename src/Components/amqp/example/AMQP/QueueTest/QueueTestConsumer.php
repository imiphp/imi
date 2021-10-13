<?php

declare(strict_types=1);

namespace AMQPApp\AMQP\QueueTest;

use Imi\Bean\Annotation\Bean;
use Imi\Queue\Contract\IMessage;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Service\BaseQueueConsumer;
use Imi\Redis\Redis;

/**
 * @Bean("QueueTestConsumer")
 */
class QueueTestConsumer extends BaseQueueConsumer
{
    /**
     * {@inheritDoc}
     */
    protected function consume(IMessage $message, IQueueDriver $queue): void
    {
        var_dump(__CLASS__, $message->getMessage(), \get_class($message));
        $queueTestMessage = QueueTestMessage::fromMessage($message->getMessage());
        Redis::set('imi-amqp:consume:QueueTest:' . $queueTestMessage->getMemberId(), $message->getMessage());

        $queue->success($message);
    }
}
