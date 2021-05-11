<?php

namespace AMQPApp\AMQP\Test2;

use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Annotation\Publisher;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BasePublisher;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestPublisher2")
 * @Publisher(tag="tag-imi", queue="queue-imi-2", exchange="exchange-imi", routingKey="imi-2")
 * @Queue(name="queue-imi-2", routingKey="imi-2")
 * @Exchange(name="exchange-imi")
 */
class TestPublisher2 extends BasePublisher
{
}
