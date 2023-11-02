<?php

declare(strict_types=1);

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 命令行注解.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Cli\Parser\ToolParser::class)]
class Command extends Base
{
    public function __construct(
        /**
         * 命令行名称.
         */
        public ?string $name = null,
        /**
         * 命令名分割符.
         */
        public string $separator = '/'
    ) {
    }
}
