<?php

declare(strict_types=1);

namespace Imi\Workerman;

use Imi\Timer\Contract\ITimer;
use Workerman\Lib\Timer;

class WorkermanTimer implements ITimer
{
    /**
     * 定时器，无限执行.
     *
     * @param int      $ms
     * @param callable $callback
     *
     * @return int
     */
    public function tick(int $ms, callable $callback): int
    {
        return Timer::add($ms / 1000, $callback);
    }

    /**
     * 定时器，只执行一次
     *
     * @param int      $ms
     * @param callable $callback
     *
     * @return int
     */
    public function after(int $ms, callable $callback): int
    {
        return Timer::add($ms / 1000, $callback, [], false);
    }

    /**
     * 删除定时器.
     *
     * @param int $id
     *
     * @return void
     */
    public function del(int $id)
    {
        Timer::del($id);
    }

    /**
     * 清空所有定时器.
     *
     * @return void
     */
    public function clear()
    {
        Timer::delAll();
    }
}
