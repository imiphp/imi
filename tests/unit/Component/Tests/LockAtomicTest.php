<?php

namespace Imi\Test\Component\Tests;

/**
 * @testdox Atomic Lock
 */
class LockAtomicTest extends BaseLockTest
{
    /**
     * @var string
     */
    protected $lockId = 'atomic';

    protected function check()
    {
        // @phpstan-ignore-next-line
        if ('Darwin' === \PHP_OS && version_compare(\SWOOLE_VERSION, '4.5.3', '<'))
        {
            $this->markTestSkipped('bug');
        }
    }
}
