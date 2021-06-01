<?php

declare(strict_types=1);

namespace AMQPApp\AMQP\Test;

use Imi\AMQP\Annotation\Connection;
use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Annotation\Publisher;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BasePublisher;
use Imi\Bean\Annotation\Bean;

/**
 * @Bean("TestPublisher")
 * @Connection(host=AMQP_SERVER_HOST, port=5672, user="guest", password="guest")
 * @Publisher(tag="tag-imi", queue="queue-imi-1", exchange="exchange-imi", routingKey="imi-1")
 * @Queue(name="queue-imi-1", routingKey="imi-1")
 * @Exchange(name="exchange-imi")
 */
class TestPublisher extends BasePublisher
{
}
