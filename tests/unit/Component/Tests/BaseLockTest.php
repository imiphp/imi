<?php
namespace Imi\Test\Component\Tests;

use Imi\Lock\Lock;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;

abstract class BaseLockTest extends BaseTest
{
    protected $lockId;

    public function testLockAndUnlock()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        $result = Lock::lock($this->lockId);
        try {
            Assert::assertTrue($result);
            Assert::assertTrue(Lock::isLocked($this->lockId));
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            Assert::assertTrue(Lock::unlock($this->lockId));
            Assert::assertFalse(Lock::isLocked($this->lockId));
        }
    }

    public function testTryLock()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        $result = Lock::tryLock($this->lockId);
        try {
            Assert::assertTrue($result);
            Assert::assertTrue(Lock::isLocked($this->lockId));
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            Assert::assertTrue(Lock::unlock($this->lockId));
            Assert::assertFalse(Lock::isLocked($this->lockId));
        }
    }

    public function testLockCallable()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        $result = Lock::lock($this->lockId, function(){
            Assert::assertTrue(Lock::isLocked($this->lockId));
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
    }

    public function testTryLockCallable()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        $result = Lock::tryLock($this->lockId, function(){
            Assert::assertTrue(Lock::isLocked($this->lockId));
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
    }

    public function testCancelLockCallabale()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        $result = Lock::lock($this->lockId, function(){
            Assert::assertTrue(false);
        }, function(){
            return true;
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
    }

}
