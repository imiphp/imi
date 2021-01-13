<?php

declare(strict_types=1);

namespace Imi\Server\Http\Controller;

use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Swoole\Server\Annotation\ServerInject;

/**
 * 单例 Http 控制器.
 */
abstract class SingletonHttpController extends HttpController
{
    /**
     * 请求
     *
     * @ServerInject("HttpRequestProxy")
     *
     * @var \Imi\Server\Http\Message\Contract\IHttpRequest
     */
    public IHttpRequest $request;

    /**
     * 响应.
     *
     * @ServerInject("HttpResponseProxy")
     *
     * @var \Imi\Server\Http\Message\Proxy\ResponseProxy
     */
    public IHttpResponse $response;
}
