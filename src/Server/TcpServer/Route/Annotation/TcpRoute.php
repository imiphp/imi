<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 路由注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\TcpServer\Parser\TcpControllerParser::class)]
class TcpRoute extends Base implements \Stringable
{
    public function __toString(): string
    {
        return http_build_query($this->toArray());
    }

    public function __construct(
        /**
         * 条件.
         */
        public array $condition = []
    ) {
    }
}
