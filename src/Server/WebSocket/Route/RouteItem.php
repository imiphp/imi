<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route;

use Imi\Server\WebSocket\Route\Annotation\WSRoute;

class RouteItem
{
    /**
     * 注解.
     */
    public WSRoute $annotation;

    /**
     * 回调.
     *
     * @var callable|\Imi\Server\Route\RouteCallable
     */
    public $callable;

    /**
     * 中间件列表.
     */
    public array $middlewares = [];

    /**
     * 其它配置项.
     */
    public array $options = [];

    /**
     * @param callable|\Imi\Server\Route\RouteCallable $callable
     */
    public function __construct(WSRoute $annotation, $callable, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->options = $options;
    }
}
