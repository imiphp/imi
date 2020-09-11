<?php

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 命令行注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
class Command extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 命令行名称.
     *
     * @var string
     */
    public ?string $name = null;
}
