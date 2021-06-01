<?php

declare(strict_types=1);

namespace Imi\Queue\Test\Queue;

use Imi\App;
use Imi\Queue\Driver\IQueueDriver;

class RedisQueueTest extends BaseQueueTest
{
    protected function getDriver(): IQueueDriver
    {
        return App::getBean('RedisQueueDriver', 'imi-queue-test');
    }
}
