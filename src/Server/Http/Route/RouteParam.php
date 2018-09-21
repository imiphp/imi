<?php
namespace Imi\Server\Http\Route;

use Imi\Server\Route\BaseRouteParam;

class RouteParam extends BaseRouteParam
{
    /**
     * swoole 请求对象
     * @var \Imi\Server\Http\Message\Request
     */
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
}