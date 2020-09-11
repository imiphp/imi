<?php

namespace Imi\Test\Component\Lock\Classes;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Annotation\Lockable;

/**
 * @Bean("TestLockAnnotation")
 */
class TestLockAnnotation
{
    /**
     * @Lockable(id="锁ID", afterLock={"$this", "check"})
     */
    public function index()
    {
        return 1;
    }

    protected function check()
    {
        return 2;
    }

    /**
     * @Lockable(id="锁ID", afterLock={"$this", "check2"})
     */
    public function index2()
    {
        return 3;
    }

    protected function check2()
    {
    }

    /**
     * @Lockable(id="锁ID")
     */
    public function test()
    {
        usleep(100000);

        return time();
    }
}
