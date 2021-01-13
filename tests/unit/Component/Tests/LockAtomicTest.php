<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

/**
 * @testdox Atomic Lock
 */
class LockAtomicTest extends BaseLockTest
{
    protected $lockId = 'atomic';

    protected function check()
    {
    }
}
