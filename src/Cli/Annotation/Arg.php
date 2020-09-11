<?php

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Parser;

/**
 * 可选项注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
class Arg extends Option
{
    /**
     * 注解别名.
     *
     * @var string|string[]
     */
    protected $__alias = Option::class;
}
