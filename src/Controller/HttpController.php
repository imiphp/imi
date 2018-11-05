<?php
namespace Imi\Controller;

use Imi\Util\Traits\TBeanClone;


/**
 * Http 控制器
 */
abstract class HttpController
{
    use TBeanClone;

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