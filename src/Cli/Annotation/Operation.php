<?php
namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Parser;

/**
 * 命令行动作注解
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
class Operation extends CommandAction
{
    /**
     * 注解别名
     *
     * @var string|string[]
     */
    protected $__alias = CommandAction::class;

}