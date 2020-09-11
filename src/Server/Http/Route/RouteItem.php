<?php

namespace Imi\Server\Http\Route;

use Imi\Server\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;

class RouteItem
{
    /**
     * 注解.
     *
     * @var \Imi\Server\Route\Annotation\Route
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
     * WebSocket 配置.
     *
     * @var array
     */
    public $wsConfig = [];

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

    /**
     * 视图注解.
     *
     * @var \Imi\Server\View\Annotation\View
     */
    public $view;

    public function __construct(Route $annotation, $callable, View $view, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->view = $view;
        $this->options = $options;
    }
}
