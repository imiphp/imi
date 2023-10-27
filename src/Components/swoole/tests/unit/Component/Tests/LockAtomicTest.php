<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Tests;

use Imi\Test\Component\Tests\BaseLockTestCase;

/**
 * @testdox Atomic Lock
 */
class LockAtomicTest extends BaseLockTestCase
{
    protected ?string $lockConfigId = 'atomic';

    protected ?string $lockId = 'imi';

    protected function check(): void
    {
    }
}
