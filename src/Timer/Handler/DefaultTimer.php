<?php

declare(strict_types=1);

namespace Imi\Timer\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Timer\Contract\ITimer;

/**
 * 默认定时器，无定时功能，立即执行.
 *
 * @Bean("DefaultTimer")
 */
class DefaultTimer implements ITimer
{
    /**
     * {@inheritDoc}
     */
    public function tick(int $ms, callable $callback): int
    {
        $callback();

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function after(int $ms, callable $callback): int
    {
        $callback();

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function del(int $id): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
    }
}
