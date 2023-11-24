<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests;

use Imi\Lock\Lock;
use Imi\RequestContext;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

abstract class BaseLockTestCase extends BaseTest
{
    protected ?string $lockConfigId = null;

    protected ?string $lockId = null;

    protected function check(): void
    {
    }

    public function testLockAndUnlock(): void
    {
        $this->check();
        foreach ([null, $this->lockId] as $lockId)
        {
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            $result = Lock::lock($this->lockConfigId, null, null, $lockId);
            try
            {
                Assert::assertTrue($result);
                Assert::assertTrue(Lock::isLocked($this->lockConfigId, $lockId));
                Assert::assertEquals(RequestContext::getCurrentId(), Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            }
            finally
            {
                Assert::assertTrue(Lock::unlock($this->lockConfigId, $lockId));
                Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
                Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            }
        }
    }

    public function testTryLock(): void
    {
        $this->check();
        foreach ([null, $this->lockId] as $lockId)
        {
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            $result = Lock::tryLock($this->lockConfigId, null, $lockId);
            try
            {
                Assert::assertTrue($result);
                Assert::assertTrue(Lock::isLocked($this->lockConfigId, $lockId));
                Assert::assertEquals(RequestContext::getCurrentId(), Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            }
            finally
            {
                Assert::assertTrue(Lock::unlock($this->lockConfigId, $lockId));
                Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
                Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            }
        }
    }

    public function testLockCallable(): void
    {
        $this->check();
        foreach ([null, $this->lockId] as $lockId)
        {
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
            $result = Lock::lock($this->lockConfigId, function () use ($lockId): void {
                Assert::assertTrue(Lock::isLocked($this->lockConfigId, $lockId));
                Assert::assertEquals(RequestContext::getCurrentId(), Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            }, null, $lockId);
            Assert::assertTrue($result);
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
        }
    }

    public function testTryLockCallable(): void
    {
        $this->check();
        foreach ([null, $this->lockId] as $lockId)
        {
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            $result = Lock::tryLock($this->lockConfigId, function () use ($lockId): void {
                Assert::assertTrue(Lock::isLocked($this->lockConfigId, $lockId));
                Assert::assertEquals(RequestContext::getCurrentId(), Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            }, $lockId);
            Assert::assertTrue($result);
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
        }
    }

    public function testCancelLockCallabale(): void
    {
        $this->check();
        foreach ([null, $this->lockId] as $lockId)
        {
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
            $result = Lock::lock($this->lockConfigId, static function (): void {
                Assert::assertTrue(false);
            }, function () use ($lockId) {
                Assert::assertEquals(RequestContext::getCurrentId(), Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());

                return true;
            }, $lockId);
            Assert::assertTrue($result);
            Assert::assertFalse(Lock::isLocked($this->lockConfigId, $lockId));
            Assert::assertEquals('', Lock::getInstance($this->lockConfigId, $lockId)->getLockFlag());
        }
    }
}
