<?php
namespace Imi\Server\WebSocket\Route;

use Imi\Server\Route\RouteCallable;

class RouteResult
{
    /**
     * 路由配置项
     *
     * @var \Imi\Server\WebSocket\Route\RouteItem
     */
    public $routeItem;

    /**
     * 参数
     *
     * @var array
     */
    public $params;

    /**
     * 回调
     *
     * @var callable
     */
    public $callable;

    public function __construct(RouteItem $routeItem, $params = [])
    {
        $this->routeItem = $routeItem;
        $this->params = $params;
        if($this->routeItem->callable instanceof RouteCallable)
        {
            $this->callable = $this->routeItem->callable->getCallable($params);
        }
        else
        {
            $this->callable = $this->routeItem->callable;
        }
    }

}