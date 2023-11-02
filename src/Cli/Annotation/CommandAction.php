<?php

declare(strict_types=1);

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 命令行动作注解.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Cli\Parser\ToolParser::class)]
class CommandAction extends Base
{
    public function __construct(
        /**
         * 操作名称.
         */
        public ?string $name = null,
        /**
         * 操作描述.
         */
        public ?string $description = null,
        /**
         * 是否启用动态参数支持
         */
        public bool $dynamicOptions = false
    ) {
    }
}
