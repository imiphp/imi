<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 路由注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\UdpServer\Parser\UdpControllerParser::class)]
class UdpRoute extends Base implements \Stringable
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
