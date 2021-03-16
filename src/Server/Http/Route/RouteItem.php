<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route;

use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;
use Imi\Server\WebSocket\Route\Annotation\WSConfig;

class RouteItem
{
    /**
     * 注解.
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
     */
    public array $options = [];

    /**
     * 是否为单例控制器.
     */
    public bool $singleton = false;

    /**
     * 视图注解.
     */
    public View $view;

    /**
     * @param callable|\Imi\Server\Route\RouteCallable $callable
     */
    public function __construct(Route $annotation, $callable, View $view, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->view = $view;
        $this->options = $options;
    }
}
