<?php

declare(strict_types=1);

namespace Imi\Server\Http\Error;

use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 处理未找到 Http 路由情况的接口.
 */
interface IHttpNotFoundHandler
{
    public function handle(RequestHandlerInterface $requesthandler, IHttpRequest $request, IHttpResponse $response): IHttpResponse;
}
