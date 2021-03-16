<?php

declare(strict_types=1);

namespace Imi\Timer\Contract;

interface ITimer
{
    /**
     * 定时器，无限执行.
     */
    public function tick(int $ms, callable $callback): int;

    /**
     * 定时器，只执行一次
     */
    public function after(int $ms, callable $callback): int;

    /**
     * 删除定时器.
     */
    public function del(int $id): void;

    /**
     * 清空所有定时器.
     */
    public function clear(): void;
}
