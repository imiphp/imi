<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\Timer\Contract\ITimer;
use Swoole\Timer;

class SwooleTimer implements ITimer
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
        return Timer::tick($ms, $callback);
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
        return Timer::after($ms, $callback);
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
        Timer::clear($id);
    }

    /**
     * 清空所有定时器.
     *
     * @return void
     */
    public function clear()
    {
        Timer::clearAll();
    }
}
