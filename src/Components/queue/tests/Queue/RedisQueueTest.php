<?php

declare(strict_types=1);

namespace Imi\Queue\Test\Queue;

use Imi\App;
use Imi\Queue\Driver\IQueueDriver;

class RedisQueueTest extends BaseQueueTest
{
    protected function getDriver(): IQueueDriver
    {
        // @phpstan-ignore-next-line
        return App::getContainer()->newInstance('RedisQueueDriver', 'imi-queue-test');
    }
}
