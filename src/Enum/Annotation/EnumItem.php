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
 * @Target("CONST")
 * @Parser("Imi\Enum\Annotation\Parser\EnumParser")
 *
 * @property string $text 文本描述
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
class EnumItem extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'text';

    public function __construct(?array $__data = null, string $text = '')
    {
        parent::__construct(...\func_get_args());
    }
}
