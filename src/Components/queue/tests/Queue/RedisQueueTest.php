<?php

declare(strict_types=1);

namespace Imi\Queue\Test\Queue;

use Imi\App;
use Imi\Queue\Driver\IQueueDriver;

class RedisQueueTest extends BaseQueueTestCase
{
    protected function getDriver(): IQueueDriver
    {
        // @phpstan-ignore-next-line
        return App::newInstance('RedisQueueDriver', 'imi-queue-test');
    }
}
