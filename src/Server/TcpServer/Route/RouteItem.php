<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route;

use Imi\Server\TcpServer\Route\Annotation\TcpRoute;

class RouteItem
{
    /**
     * 注解.
     */
    public TcpRoute $annotation;

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
     * 是否为单例控制器.
     */
    public bool $singleton = false;

    /**
     * @param callable|\Imi\Server\Route\RouteCallable $callable
     */
    public function __construct(TcpRoute $annotation, $callable, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->options = $options;
    }
}
