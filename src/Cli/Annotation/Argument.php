<?php

declare(strict_types=1);

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 参数注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Cli\Parser\ToolParser::class)]
class Argument extends Base
{
    public function __construct(
        /**
         * 参数名称.
         */
        public string $name = '',
        /**
         * 参数类型.
         */
        public ?string $type = null,
        /**
         * 默认值
         *
         * @var mixed
         */
        public $default = null,
        /**
         * 是否是必选参数.
         */
        public bool $required = false,
        /**
         * 注释.
         */
        public string $comments = '',
        /**
         * 将参数值绑定到指定名称的参数.
         */
        public string $to = ''
    ) {
    }
}
