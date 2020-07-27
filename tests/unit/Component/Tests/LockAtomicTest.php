<?php
namespace Imi\Test\Component\Tests;

/**
 * @testdox Atomic Lock
 */
class LockAtomicTest extends BaseLockTest
{
    protected $lockId = 'atomic';

    protected function check()
    {
        if('Darwin' === PHP_OS && version_compare(SWOOLE_VERSION, '4.5.3', '<'))
        {
            $this->markTestSkipped('bug');
        }
    }

}
