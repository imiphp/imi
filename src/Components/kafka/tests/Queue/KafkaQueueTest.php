<?php

namespace Imi\Kafka\Test\Queue;

use Imi\App;
use Imi\Queue\Driver\IQueueDriver;

class KafkaQueueTest extends BaseQueueTest
{
    protected function getDriver(): IQueueDriver
    {
        return App::getBean('KafkaQueueDriver', 'imi-kafka-queue-test', [
            'poolName' => 'kafka',
        ]);
    }
}
