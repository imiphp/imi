<?php

declare(strict_types=1);

namespace Imi\Async\Contract;

/**
 * 异步执行器.
 */
interface IAsyncHandler
{
    /**
     * 执行.
     */
    public function exec(callable $callable): IAsyncResult;
}
