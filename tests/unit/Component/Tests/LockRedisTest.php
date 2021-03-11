<?php

namespace Imi\Test\Component\Tests;

/**
 * @testdox Redis Lock
 */
class LockRedisTest extends BaseLockTest
{
    /**
     * @var string
     */
    protected $lockId = 'redis';
}
