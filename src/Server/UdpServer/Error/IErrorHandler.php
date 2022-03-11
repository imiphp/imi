<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Error;

/**
 * UdpServer 服务错误捕获器.
 */
interface IErrorHandler
{
    /**
     * 捕获错误
     * 返回值为 true 则取消继续抛出异常.
     */
    public function handle(\Throwable $throwable): bool;
}
