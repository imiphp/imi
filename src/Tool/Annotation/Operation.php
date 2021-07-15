<?php

declare(strict_types=1);

namespace Imi\Tool\Annotation;

use Imi\Bean\Annotation\Parser;
use Imi\Cli\Annotation\CommandAction;

/**
 * 命令行动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Operation extends CommandAction
{
    /**
     * 注解别名.
     *
     * @var string|string[]
     */
    protected $__alias = CommandAction::class;
}
