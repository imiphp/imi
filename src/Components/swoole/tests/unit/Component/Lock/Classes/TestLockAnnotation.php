<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component\Lock\Classes;

use Imi\Bean\Annotation\Bean;
use Imi\Lock\Annotation\Lockable;

#[Bean(name: 'TestLockAnnotation')]
class TestLockAnnotation
{
    #[Lockable(id: '锁ID', afterLock: ['$this', 'check'])]
    public function index(): int
    {
        return 1;
    }

    protected function check(): int
    {
        return 2;
    }

    #[Lockable(id: '锁ID', afterLock: ['$this', 'check2'])]
    public function index2(): int
    {
        return 3;
    }

    protected function check2(): void
    {
    }

    #[Lockable(id: '锁ID')]
    public function test(): int
    {
        usleep(100000);

        return time();
    }
}
