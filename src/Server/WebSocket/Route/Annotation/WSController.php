<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 控制器注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Server\WebSocket\Parser\WSControllerParser")
 */
#[\Attribute]
class WSController extends Base
{
    /**
     * http 路由.
     *
     * 如果设置，则只有握手指定 http 路由，才可以触发该 WebSocket 路由
     */
    public ?string $route = null;

    public function __construct(?array $__data = null, ?string $route = null)
    {
        parent::__construct(...\func_get_args());
    }
}
