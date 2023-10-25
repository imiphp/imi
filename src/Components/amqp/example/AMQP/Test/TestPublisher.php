<?php

declare(strict_types=1);

namespace AMQPApp\AMQP\Test;

use Imi\AMQP\Annotation\Exchange;
use Imi\AMQP\Annotation\Publisher;
use Imi\AMQP\Annotation\Queue;
use Imi\AMQP\Base\BasePublisher;
use Imi\Bean\Annotation\Bean;

#[Bean(name: 'TestPublisher')]
#[Publisher(queue: 'queue-imi-2', exchange: 'exchange-imi', routingKey: 'imi-2')]
#[Queue(name: 'queue-imi-2', routingKey: 'imi-2')]
#[Exchange(name: 'exchange-imi')]
class TestPublisher extends BasePublisher
{
}
