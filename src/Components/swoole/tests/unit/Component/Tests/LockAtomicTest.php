<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\Test\Component\Tests\BaseLockTest;

/**
 * @testdox Atomic Lock
 */
class LockAtomicTest extends BaseLockTest
{
    protected ?string $lockConfigId = 'atomic';

    protected ?string $lockId = 'imi';

    protected function check(): void
    {
    }
}
