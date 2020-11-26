<?php

namespace Imi\Server\Http\Controller;

use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;

/**
 * 单例 Http 控制器.
 */
abstract class SingletonHttpController extends HttpController
{
    /**
     * 请求
     *
     * @var \Imi\Server\Http\Message\Contract\IHttpRequest
     */
    public IHttpRequest $request;

    /**
     * 响应.
     *
     * @var \Imi\Server\Http\Message\Proxy\ResponseProxy
     */
    public IHttpResponse $response;
}
