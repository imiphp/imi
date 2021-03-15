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
     *
     * @var \Imi\Server\Http\Route\Annotation\Route
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
    public array $options = [];

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

    /**
     * @param \Imi\Server\Http\Route\Annotation\Route  $annotation
     * @param callable|\Imi\Server\Route\RouteCallable $callable
     * @param \Imi\Server\View\Annotation\View         $view
     * @param array                                    $options
     */
    public function __construct(Route $annotation, $callable, View $view, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->view = $view;
        $this->options = $options;
    }
}
