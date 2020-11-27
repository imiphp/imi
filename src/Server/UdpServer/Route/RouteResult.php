<?php

namespace Imi\Server\UdpServer\Route;

use Imi\Server\Route\RouteCallable;
use Imi\Server\UdpServer\Route\RouteItem;

class RouteResult
{
    /**
     * 路由配置项.
     *
     * @var \Imi\Server\UdpServer\Route\RouteItem
     */
    public RouteItem $routeItem;

    /**
     * 参数.
     *
     * @var array
     */
    public array $params;

    /**
     * 回调.
     *
     * @var callable
     */
    public $callable;

    public function __construct(RouteItem $routeItem, array$params = [])
    {
        $this->routeItem = $routeItem;
        $this->params = $params;
        $callable = $routeItem->callable;
        if ($callable instanceof RouteCallable)
        {
            $this->callable = $callable->getCallable($params);
        }
        else
        {
            $this->callable = $callable;
        }
    }
}
