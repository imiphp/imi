<?php

declare(strict_types=1);

namespace Imi\Enum\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * enum 枚举项.
 *
 * @Annotation
 *
 * @Target("CONST")
 *
 * @property string $text 文本描述
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
#[Parser(className: \Imi\Enum\Annotation\Parser\EnumParser::class)]
class EnumItem extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'text';

    public function __construct(?array $__data = null, string $text = '')
    {
        parent::__construct(...\func_get_args());
    }
}
