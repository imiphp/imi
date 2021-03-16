<?php

declare(strict_types=1);

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
#[\Attribute]
class CommandAction extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    /**
     * 操作名称.
     */
    public ?string $name = null;

    /**
     * 是否启用动态参数支持
     */
    public bool $dynamicOptions = false;

    public function __construct(?array $__data = null, ?string $name = null, bool $dynamicOptions = false)
    {
        parent::__construct(...\func_get_args());
    }
}
