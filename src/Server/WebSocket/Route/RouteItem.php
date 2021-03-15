<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route;

use Imi\Server\WebSocket\Route\Annotation\WSRoute;

class RouteItem
{
    /**
     * 注解.
     *
     * @var \Imi\Server\WebSocket\Route\Annotation\WSRoute
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
     *
     * @var array
     */
    public array $middlewares = [];

    /**
     * 其它配置项.
     *
     * @var array
     */
    public array $options = [];

    /**
     * 是否为单例控制器.
     *
     * @var bool
     */
    public bool $singleton = false;

    /**
     * @param \Imi\Server\WebSocket\Route\Annotation\WSRoute $annotation
     * @param callable|\Imi\Server\Route\RouteCallable       $callable
     * @param array                                          $options
     */
    public function __construct(WSRoute $annotation, $callable, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->options = $options;
    }
}
