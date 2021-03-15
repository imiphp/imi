<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\Test\Component\Tests\BaseLockTest;

/**
 * @testdox Atomic Lock
 */
class LockAtomicTest extends BaseLockTest
{
    protected string $lockId = 'atomic';

    protected function check(): void
    {
    }
}
