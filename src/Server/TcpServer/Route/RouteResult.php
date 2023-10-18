<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route;

class RouteResult
{
    /**
     * 回调.
     *
     * @var callable
     */
    public $callable;

    public function __construct(/**
     * 路由配置项.
     */
        public RouteItem $routeItem, /**
     * 参数.
     */
        public array $params = [])
    {
        $this->callable = $routeItem->callable;
    }
}
