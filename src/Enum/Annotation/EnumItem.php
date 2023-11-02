<?php

declare(strict_types=1);

namespace Imi\Enum\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * enum 枚举项.
 */
#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
#[Parser(className: \Imi\Enum\Annotation\Parser\EnumParser::class)]
class EnumItem extends Base
{
    public function __construct(
        /**
         * 文本描述.
         */
        public string $text = ''
    ) {
    }
}
