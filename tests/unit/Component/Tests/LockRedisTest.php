<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

/**
 * @testdox Redis Lock
 */
class LockRedisTest extends BaseLockTest
{
    protected $lockId = 'redis';
}
