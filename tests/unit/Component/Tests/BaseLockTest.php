<?php
namespace Imi\Test\Component\Tests;

use Imi\Lock\Lock;
use Imi\Test\BaseTest;
use PHPUnit\Framework\Assert;
use Swoole\Coroutine;

abstract class BaseLockTest extends BaseTest
{
    protected $lockId;

    public function testLockAndUnlock()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
        $result = Lock::lock($this->lockId);
        try {
            Assert::assertTrue($result);
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(Coroutine::getuid(), Lock::getInstance($this->lockId)->getLockCoId());
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            Assert::assertTrue(Lock::unlock($this->lockId));
            Assert::assertFalse(Lock::isLocked($this->lockId));
            Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
        }
    }

    public function testTryLock()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
        $result = Lock::tryLock($this->lockId);
        try {
            Assert::assertTrue($result);
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(Coroutine::getuid(), Lock::getInstance($this->lockId)->getLockCoId());
        } catch(\Throwable $th) {
            throw $th;
        } finally {
            Assert::assertTrue(Lock::unlock($this->lockId));
            Assert::assertFalse(Lock::isLocked($this->lockId));
            Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
        }
    }

    public function testLockCallable()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        $result = Lock::lock($this->lockId, function(){
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(Coroutine::getuid(), Lock::getInstance($this->lockId)->getLockCoId());
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
    }

    public function testTryLockCallable()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
        $result = Lock::tryLock($this->lockId, function(){
            Assert::assertTrue(Lock::isLocked($this->lockId));
            Assert::assertEquals(Coroutine::getuid(), Lock::getInstance($this->lockId)->getLockCoId());
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
    }

    public function testCancelLockCallabale()
    {
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
        $result = Lock::lock($this->lockId, function(){
            Assert::assertTrue(false);
        }, function(){
            Assert::assertEquals(Coroutine::getuid(), Lock::getInstance($this->lockId)->getLockCoId());
            return true;
        });
        Assert::assertTrue($result);
        Assert::assertFalse(Lock::isLocked($this->lockId));
        Assert::assertEquals(-1, Lock::getInstance($this->lockId)->getLockCoId());
    }

}
