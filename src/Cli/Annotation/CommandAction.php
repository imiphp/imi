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
 *
 * @property string|null $name           操作名称
 * @property string|null $description    操作描述
 * @property bool        $dynamicOptions 是否启用动态参数支持
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class CommandAction extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, ?string $name = null, ?string $description = null, bool $dynamicOptions = false)
    {
        parent::__construct(...\func_get_args());
    }
}
