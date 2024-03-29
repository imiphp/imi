<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 控制器注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Server\Http\Parser\ControllerParser::class)]
class Controller extends Base
{
    public function __construct(
        /**
         * 路由前缀
         */
        public ?string $prefix = null,
        /**
         * 指定当前控制器允许哪些服务器使用；支持字符串或数组，默认为 null 则不限制.
         *
         * @var string|string[]|null
         */
        public $server = null
    ) {
    }
}
