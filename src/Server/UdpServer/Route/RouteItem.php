<?php

namespace Imi\Server\UdpServer\Route;

use Imi\Server\Route\Annotation\Udp\UdpRoute;

class RouteItem
{
    /**
     * 注解.
     *
     * @var \Imi\Server\Route\Annotation\Udp\UdpRoute
     */
    public $annotation;

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
    public $middlewares = [];

    /**
     * 其它配置项.
     *
     * @var array
     */
    public $options;

    /**
     * 是否为单例控制器.
     *
     * @var bool
     */
    public $singleton = false;

    public function __construct(UdpRoute $annotation, $callable, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->options = $options;
    }
}
