<?php

declare(strict_types=1);

namespace Imi\Cli\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 命令行注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Cli\Parser\ToolParser")
 *
 * @property string|null $name 命令行名称
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Command extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, ?string $name = null)
    {
        parent::__construct(...\func_get_args());
    }
}
