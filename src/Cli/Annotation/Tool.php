<?php
namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Parser;

/**
 * 命令行注解
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
class Tool extends Command
{
    /**
     * 注解别名
     *
     * @var string|string[]
     */
    protected $__alias = Command::class;

} 