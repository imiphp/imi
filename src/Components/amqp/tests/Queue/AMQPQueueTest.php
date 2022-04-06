<?php

declare(strict_types=1);

namespace Imi\AMQP\Test\Queue;

use Imi\App;
use Imi\Queue\Driver\IQueueDriver;

class AMQPQueueTest extends BaseQueueTest
{
    protected function getDriver(string $name = 'imi-amqp-queue-test'): IQueueDriver
    {
        // @phpstan-ignore-next-line
        return App::getBean('AMQPQueueDriver', $name, [
            'poolName'          => 'rabbit',
            'redisPoolName'     => 'redis',
        ]);
    }
}
