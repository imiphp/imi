<?php

namespace Imi\Controller;

/**
 * Http 控制器.
 */
abstract class HttpController
{
    /**
     * 请求
     *
     * @var \Imi\Server\Http\Message\Request
     */
    public $request;

    /**
     * 响应.
     *
     * @var \Imi\Server\Http\Message\Response
     */
    public $response;

    /**
     * 渲染页面.
     *
     * @param string $template 模版路径。例：abc-配置中设定的路径/abc/；/abc/-绝对路径
     * @param array  $data     渲染用数据
     *
     * @return \Imi\Server\View\Annotation\View
     */
    protected function __render($template, $data = [])
    {
        return new \Imi\Server\View\Annotation\View([
            'template'      => $template,
            'renderType'    => 'html',
            'data'          => $data,
        ]);
    }
}
