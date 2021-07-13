<?php

declare(strict_types=1);

namespace KafkaApp\Kafka\Test;

use Imi\Bean\Annotation\Bean;
use Imi\Kafka\Annotation\Consumer;
use Imi\Kafka\Base\BaseConsumer;
use Imi\Redis\Redis;
use longlang\phpkafka\Consumer\ConsumeMessage;

/**
 * @Bean("TestConsumer")
 * @Consumer(topic="queue-imi-1", groupId="test-consumer")
 */
class TestConsumer extends BaseConsumer
{
    /**
     * 消费任务
     */
    protected function consume(ConsumeMessage $message): void
    {
        $data = json_decode($message->getValue(), true);
        var_dump(__CLASS__, $message->getValue());
        Redis::set('imi-kafka:consume:1:' . $data['memberId'], $message->getValue());
    }
}
