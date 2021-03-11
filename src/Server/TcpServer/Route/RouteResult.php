<?php

namespace Imi\Server\TcpServer\Route;

use Imi\Server\Route\RouteCallable;

class RouteResult
{
    /**
     * 路由配置项.
     *
     * @var \Imi\Server\TcpServer\Route\RouteItem
     */
    public $routeItem;

    /**
     * 参数.
     *
     * @var array
     */
    public $params;

    /**
     * 回调.
     *
     * @var callable
     */
    public $callable;

    /**
     * @param RouteItem $routeItem
     * @param array     $params
     */
    public function __construct(RouteItem $routeItem, $params = [])
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
