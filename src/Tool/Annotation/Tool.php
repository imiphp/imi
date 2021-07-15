<?php

declare(strict_types=1);

namespace Imi\Tool\Annotation;

use Imi\Bean\Annotation\Parser;
use Imi\Cli\Annotation\Command;

/**
 * 命令行注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Tool extends Command
{
    /**
     * 注解别名.
     *
     * @var string|string[]
     */
    protected $__alias = Command::class;
}
