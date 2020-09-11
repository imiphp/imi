<?php

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 命令行动作注解.
 *
 * @Annotation
 * @Target("METHOD")
 * @Parser("Imi\Cli\Parser\ToolParser")
 */
class CommandAction extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 操作名称.
     *
     * @var string|null
     */
    public ?string $name = null;

    /**
     * 是否启用动态参数支持
     *
     * @var bool
     */
    public bool $dynamicOptions = false;
}
