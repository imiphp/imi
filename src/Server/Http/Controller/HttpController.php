<?php

declare(strict_types=1);

namespace Imi\Server\Http\Controller;

use Imi\Server\Annotation\ServerInject;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\View;

/**
 * Http 控制器.
 */
abstract class HttpController
{
    /**
     * 请求
     *
     * @ServerInject("HttpRequestProxy")
     */
    public IHttpRequest $request;

    /**
     * 响应.
     *
     * @ServerInject("HttpResponseProxy")
     */
    public IHttpResponse $response;

    /**
     * 渲染页面.
     *
     * @param string $template 模版路径。例：abc-配置中设定的路径/abc/；/abc/-绝对路径
     * @param array  $data     渲染用数据
     */
    protected function __render(string $template, array $data = []): View
    {
        return new View([
            'template'      => $template,
            'renderType'    => 'html',
            'data'          => $data,
        ]);
    }
}
