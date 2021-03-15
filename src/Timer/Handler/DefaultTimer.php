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
     * 定时器，无限执行.
     *
     * @param int      $ms
     * @param callable $callback
     *
     * @return int
     */
    public function tick(int $ms, callable $callback): int
    {
        $callback();

        return 0;
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
        $callback();

        return 0;
    }

    /**
     * 删除定时器.
     *
     * @param int $id
     *
     * @return void
     */
    public function del(int $id): void
    {
    }

    /**
     * 清空所有定时器.
     *
     * @return void
     */
    public function clear(): void
    {
    }
}
