<?php

declare(strict_types=1);

namespace Imi\AMQP\Test\Queue;

use Imi\App;
use Imi\Queue\Driver\IQueueDriver;

class AMQPQueueTest extends BaseQueueTest
{
    protected function getDriver(): IQueueDriver
    {
        return App::getBean('AMQPQueueDriver', 'imi-amqp-queue-test', [
            'poolName'          => 'rabbit',
            'redisPoolName'     => 'redis',
        ]);
    }
}
