<?php

declare(strict_types=1);

namespace Imi\Server\Http\Controller;

use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Contract\IServer;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\View\Annotation\HtmlView;
use Imi\Server\View\Annotation\View;

/**
 * Http 控制器.
 */
abstract class HttpController
{
    /**
     * 服务器对象
     */
    public IServer $server;

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

    public function __construct()
    {
        $this->server = RequestContext::getServer();
    }

    /**
     * 渲染页面.
     *
     * @param string $template 模版路径。例：abc-配置中设定的路径/abc/；/abc/-绝对路径
     * @param array  $data     渲染用数据
     */
    protected function __render(string $template, array $data = []): View
    {
        return new View([
            'renderType' => 'html',
            'data'       => $data,
            'option'     => new HtmlView([
                'template' => $template,
            ]),
        ]);
    }
}
