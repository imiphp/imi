<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route;

use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\BaseViewOption;
use Imi\Server\View\Annotation\View;
use Imi\Server\WebSocket\Route\Annotation\WSConfig;

class RouteItem
{
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
     * WebSocket 配置.
     */
    public ?WSConfig $wsConfig = null;

    public function __construct(
        /**
         * 注解.
         */
        public ?Route $annotation, callable $callable,
        /**
         * 视图注解.
         */
        public ?View $view,
        /**
         * 视图配置注解.
         */
        public ?BaseViewOption $viewOption = null,
        /**
         * 其它配置项.
         */
        public array $options = [])
    {
        $this->callable = $callable;
    }
}
