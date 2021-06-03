<?php

declare(strict_types=1);

namespace Imi\Workerman;

use Imi\Timer\Contract\ITimer;
use Workerman\Lib\Timer;

class WorkermanTimer implements ITimer
{
    /**
     * 定时器，无限执行.
     */
    public function tick(int $ms, callable $callback): int
    {
        return Timer::add($ms / 1000, $callback);
    }

    /**
     * 定时器，只执行一次
     */
    public function after(int $ms, callable $callback): int
    {
        return Timer::add($ms / 1000, $callback, [], false);
    }

    /**
     * 删除定时器.
     */
    public function del(int $id): void
    {
        Timer::del($id);
    }

    /**
     * 清空所有定时器.
     */
    public function clear(): void
    {
        Timer::delAll();
    }
}
