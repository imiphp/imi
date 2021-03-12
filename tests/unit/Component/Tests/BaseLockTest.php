<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Lock\Lock;
use Imi\RequestContext;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

abstract class BaseLockTest extends BaseTest
{
    protected string $lockId;

    protected function check()
    {
    }

    public function testLockAndUnlock()
    {
        $this->check();
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
        $result = Lock::lock($this->lockId);
        try
        {
            Assert::assertTrue($result);
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(RequestContext::getCurrentFlag(), Lock::getInstance($this->lockId)->getLockFlag());
        }
        finally
        {
            Assert::assertTrue(Lock::unlock($this->lockId));
            Assert::assertFalse(Lock::isLocked($this->lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
        }
    }

    public function testTryLock()
    {
        $this->check();
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
        $result = Lock::tryLock($this->lockId);
        try
        {
            Assert::assertTrue($result);
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(RequestContext::getCurrentFlag(), Lock::getInstance($this->lockId)->getLockFlag());
        }
        finally
        {
            Assert::assertTrue(Lock::unlock($this->lockId));
            Assert::assertFalse(Lock::isLocked($this->lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
        }
    }

    public function testLockCallable()
    {
        $this->check();
        Assert::assertFalse(Lock::isLocked($this->lockId));
        $result = Lock::lock($this->lockId, function () {
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(RequestContext::getCurrentFlag(), Lock::getInstance($this->lockId)->getLockFlag());
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
    }

    public function testTryLockCallable()
    {
        $this->check();
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
        $result = Lock::tryLock($this->lockId, function () {
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(RequestContext::getCurrentFlag(), Lock::getInstance($this->lockId)->getLockFlag());
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
    }

    public function testCancelLockCallabale()
    {
        $this->check();
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
        $result = Lock::lock($this->lockId, function () {
            Assert::assertTrue(false);
        }, function () {
            Assert::assertEquals(RequestContext::getCurrentFlag(), Lock::getInstance($this->lockId)->getLockFlag());

            return true;
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals('', Lock::getInstance($this->lockId)->getLockFlag());
    }
}
