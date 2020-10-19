<?php

namespace Imi\Controller;

/**
 * 单例 Http 控制器.
 */
abstract class SingletonHttpController extends HttpController
{
    /**
     * 请求
     *
     * @var \Imi\Server\Http\Message\Proxy\RequestProxy
     */
    public $request;

    /**
     * 响应.
     *
     * @var \Imi\Server\Http\Message\Proxy\ResponseProxy
     */
    public $response;
}
