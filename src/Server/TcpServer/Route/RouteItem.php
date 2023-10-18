<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route;

use Imi\Server\TcpServer\Route\Annotation\TcpRoute;

class RouteItem
{
    /**
     * 回调.
     *
     * @var callable
     */
    public $callable;

    /**
     * 中间件列表.
     */
    public array $middlewares = [];

    public function __construct(/**
     * 注解.
     */
        public ?TcpRoute $annotation, callable $callable, /**
     * 其它配置项.
     */
        public array $options = [])
    {
        $this->callable = $callable;
    }
}
