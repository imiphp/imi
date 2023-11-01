<?php

declare(strict_types=1);

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Parser;

/**
 * 可选项注解.
 *
 * @Annotation
 *
 * @Target("METHOD")
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
#[Parser(className: \Imi\Cli\Parser\ToolParser::class)]
class Arg extends Option
{
    /**
     * {@inheritDoc}
     */
    protected $__alias = Option::class;
}
