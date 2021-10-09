<?php

declare(strict_types=1);

namespace Imi\Async\Contract;

/**
 * 异步执行结果.
 */
interface IAsyncResult
{
    /**
     * 获取异步返回结果.
     *
     * 默认不超时无限等待，超时则会抛出异常
     *
     * @return mixed
     */
    public function get(?float $timeout = null);
}
