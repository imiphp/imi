<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * Tcp 中间件注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\TcpServer\Parser\TcpControllerParser::class)]
class TcpMiddleware extends Base
{
    public function __construct(
        /**
         * @var string|string[]|null
         */
        public $middlewares = null
    ) {
    }
}
