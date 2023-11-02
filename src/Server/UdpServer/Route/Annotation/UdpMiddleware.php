<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Udp 中间件注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\UdpServer\Parser\UdpControllerParser::class)]
class UdpMiddleware extends Base
{
    public function __construct(
        /**
         * 中间件类或数组.
         *
         * @var string|string[]|null
         */
        public $middlewares = null
    ) {
    }
}
