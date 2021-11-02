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
     * @var callable
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

    public function __construct(WSRoute $annotation, callable $callable, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->options = $options;
    }
}
