<?php

namespace Imi\Server\Http\Route;

use Imi\Server\Route\RouteCallable;

class RouteResult
{
    /**
     * 路由配置项.
     *
     * @var \Imi\Server\Http\Route\RouteItem
     */
    public $routeItem;

    /**
     * URL 检测结果.
     *
     * @var \Imi\Server\Http\Route\UrlCheckResult
     */
    public $urlCheckResult;

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

    public function __construct(RouteItem $routeItem, UrlCheckResult $urlCheckResult, $params)
    {
        $this->routeItem = $routeItem;
        $this->urlCheckResult = $urlCheckResult;
        $this->params = $params;
        $callable = $routeItem->callable;
        if ($callable instanceof RouteCallable)
        {
            $this->callable = $callable->getCallable($urlCheckResult->params);
        }
        else
        {
            $this->callable = $callable;
        }
    }
}
