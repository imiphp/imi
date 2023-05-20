<?php

declare(strict_types=1);

namespace Imi\Cli;

/**
 * 命令行相关的应用上下文名称定义.
 */
class CliAppContexts
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 命令行名称.
     */
    public const COMMAND_NAME = 'command_name';
}
