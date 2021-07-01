<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 路由注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Server\WebSocket\Parser\WSControllerParser")
 *
 * @property array       $condition 条件
 * @property string|null $route     http 路由；如果设置，则只有握手指定 http 路由，才可以触发该 WebSocket 路由
 */
#[\Attribute]
class WSRoute extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'condition';

    public function __toString()
    {
        return http_build_query($this->toArray());
    }

    public function __construct(?array $__data = null, array $condition = [], ?string $route = null)
    {
        parent::__construct(...\func_get_args());
    }
}
