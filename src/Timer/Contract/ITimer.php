<?php

declare(strict_types=1);

namespace Imi\Timer\Contract;

interface ITimer
{
    /**
     * 定时器，无限执行.
     *
     * @param int      $ms
     * @param callable $callback
     *
     * @return int
     */
    public function tick(int $ms, callable $callback): int;

    /**
     * 定时器，只执行一次
     *
     * @param int      $ms
     * @param callable $callback
     *
     * @return int
     */
    public function after(int $ms, callable $callback): int;

    /**
     * 删除定时器.
     *
     * @param int $id
     *
     * @return void
     */
    public function del(int $id);

    /**
     * 清空所有定时器.
     *
     * @return void
     */
    public function clear();
}
