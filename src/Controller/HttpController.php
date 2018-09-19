<?php
namespace Imi\Controller;

/**
 * Http 控制器
 */
abstract class HttpController
{
    /**
     * 请求
     * @var \Imi\Server\Http\Message\Request
     */
    public $request;

    /**
     * 响应
     * @var \Imi\Server\Http\Message\Response
     */
    public $response;
    
}