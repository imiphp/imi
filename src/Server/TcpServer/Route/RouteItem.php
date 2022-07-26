<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route;

use Imi\Server\TcpServer\Route\Annotation\TcpRoute;

class RouteItem
{
    /**
     * 注解.
     */
    public ?TcpRoute $annotation = null;

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

    public function __construct(TcpRoute $annotation, callable $callable, array $options = [])
    {
        $this->annotation = $annotation;
        $this->callable = $callable;
        $this->options = $options;
    }
}
