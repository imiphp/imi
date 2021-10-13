<?php

declare(strict_types=1);

namespace Imi\Workerman;

use Imi\Timer\Contract\ITimer;
use Workerman\Lib\Timer;

class WorkermanTimer implements ITimer
{
    /**
     * {@inheritDoc}
     */
    public function tick(int $ms, callable $callback): int
    {
        return Timer::add($ms / 1000, $callback);
    }

    /**
     * {@inheritDoc}
     */
    public function after(int $ms, callable $callback): int
    {
        return Timer::add($ms / 1000, $callback, [], false);
    }

    /**
     * {@inheritDoc}
     */
    public function del(int $id): void
    {
        Timer::del($id);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        Timer::delAll();
    }
}
