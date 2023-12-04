<?php

declare(strict_types=1);

namespace Imi\Cli\Event;

use Imi\Util\Traits\TStaticClass;

final class CommandEvents
{
    use TStaticClass;

    /**
     * 命令行执行前置事件.
     */
    public const BEFORE_COMMAND = 'imi.command.before';

    /**
     * 命令行执行后置事件.
     */
    public const AFTER_COMMAND = 'imi.command.after';
}
