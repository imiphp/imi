<?php

declare(strict_types=1);

namespace Imi\Timer\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\Timer\Contract\ITimer;

/**
 * 默认定时器，无定时功能，立即执行.
 *
 * @codeCoverageIgnore
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
        throw new \RuntimeException('Unsupport tick timer');
    }

    /**
     * {@inheritDoc}
     */
    public function after(int $ms, callable $callback): int
    {
        throw new \RuntimeException('Unsupport after timer');
    }

    /**
     * {@inheritDoc}
     */
    public function del(int $id): void
    {
        throw new \RuntimeException('Unsupport del timer');
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
    }
}
