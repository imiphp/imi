<?php

namespace Imi\Server\Http\Route;

use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\WebSocket\WSConfig;
use Imi\Server\View\Annotation\View;

class RouteItem
{
    /**
     * 注解.
     *
     * @var \Imi\Server\Route\Annotation\Route
     */
    public Route $annotation;

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
     * WebSocket 配置.
     *
     * @var WSConfig
     */
    public ?WSConfig $wsConfig = null;

    /**
     * 其它配置项.
     *
     * @var array
     */
    public array $options;

    /**
     * 是否为单例控制器.
     *
     * @var bool
     */
    public bool $singleton = false;

    /**
     * 视图注解.
     *
     * @var \Imi\Server\View\Annotation\View
     */
    public View $view;

    public function __construct(Route $annotation, $callable, View $view, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->view = $view;
        $this->options = $options;
    }
}
