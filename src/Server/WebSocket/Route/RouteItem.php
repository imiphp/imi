<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route;

use Imi\Server\WebSocket\Route\Annotation\WSRoute;

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
        public ?WSRoute $annotation, callable $callable, /**
     * 其它配置项.
     */
        public array $options = [])
    {
        $this->callable = $callable;
    }
}
