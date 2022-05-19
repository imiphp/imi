<?php

declare(strict_types=1);

namespace Imi\Tool\Annotation;

use Imi\Bean\Annotation\Parser;
use Imi\Cli\Annotation\Option;

/**
 * 可选项注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Arg extends Option
{
    /**
     * {@inheritDoc}
     */
    protected $__alias = Option::class;
}
