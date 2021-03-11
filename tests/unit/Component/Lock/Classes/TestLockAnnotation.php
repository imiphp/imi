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
     *
     * @return int
     */
    public function index()
    {
        return 1;
    }

    /**
     * @return int
     */
    protected function check()
    {
        return 2;
    }

    /**
     * @Lockable(id="锁ID", afterLock={"$this", "check2"})
     *
     * @return int
     */
    public function index2()
    {
        return 3;
    }

    /**
     * @return void
     */
    protected function check2()
    {
    }

    /**
     * @Lockable(id="锁ID")
     *
     * @return int
     */
    public function test()
    {
        usleep(100000);

        return time();
    }
}
