<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * WebSocket 路由注解.
 *
 * @Annotation
 *
 * @Target("METHOD")
 *
 * @property array       $condition 条件
 * @property string|null $route     http 路由；如果设置，则只有握手指定 http 路由，才可以触发该 WebSocket 路由
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\WebSocket\Parser\WSControllerParser::class)]
class WSRoute extends Base implements \Stringable
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'condition';

    public function __toString(): string
    {
        return http_build_query($this->toArray());
    }

    public function __construct(?array $__data = null, array $condition = [], ?string $route = null)
    {
        parent::__construct(...\func_get_args());
    }
}
