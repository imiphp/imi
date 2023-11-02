<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 中间件注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\WebSocket\Parser\WSControllerParser::class)]
class WSMiddleware extends Base
{
    public function __construct(
        /**
         * 中间件类或数组.
         *
         * @var string|string[]
         */
        public $middlewares = null
    ) {
    }
}
