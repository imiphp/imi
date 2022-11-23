<?php

declare(strict_types=1);

namespace Imi\Swoole;

use Imi\Log\Log;
use Imi\Timer\Contract\ITimer;
use Swoole\Timer;

class SwooleTimer implements ITimer
{
    /**
     * {@inheritDoc}
     */
    public function tick(int $ms, callable $callback): int
    {
        return Timer::tick($ms, static function () use ($callback) {
            try
            {
                $callback();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function after(int $ms, callable $callback): int
    {
        return Timer::after($ms, static function () use ($callback) {
            try
            {
                $callback();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function del(int $id): void
    {
        Timer::clear($id);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        Timer::clearAll();
    }
}
