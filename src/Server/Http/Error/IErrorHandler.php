<?php

namespace Imi\Server\Http\Error;

/**
 * Http 服务错误捕获器.
 */
interface IErrorHandler
{
    /**
     * 捕获错误
     * 返回值为 true 则取消继续抛出异常.
     *
     * @param \Throwable $throwable
     *
     * @return bool
     */
    public function handle(\Throwable $throwable): bool;
}
