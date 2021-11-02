<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route;

class RouteResult
{
    /**
     * 路由ID.
     */
    public int $id = 0;

    /**
     * 路由配置项.
     *
     * @var \Imi\Server\Http\Route\RouteItem
     */
    public RouteItem $routeItem;

    /**
     * 参数.
     */
    public array $params = [];

    /**
     * 回调.
     *
     * @var callable
     */
    public $callable;

    public function __construct(int $id, RouteItem $routeItem, array $params)
    {
        $this->id = $id;
        $this->routeItem = $routeItem;
        $this->params = $params;
        $this->callable = $routeItem->callable;
    }
}
