<?php

declare(strict_types=1);

namespace KafkaApp\Kafka\QueueTest;

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
     * 处理消费.
     */
    protected function consume(IMessage $message, IQueueDriver $queue): void
    {
        $queueTestMessage = QueueTestMessage::fromMessage($message->getMessage());
        Redis::set('imi-kafka:consume:QueueTest:' . $queueTestMessage->getMemberId(), $message->getMessage());

        $queue->success($message);
    }
}
