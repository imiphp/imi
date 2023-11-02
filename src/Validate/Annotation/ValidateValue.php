<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 验证时的值
 */
#[\Attribute]
class ValidateValue extends Base
{
    public function __construct(
        /**
         * 值规则；支持代入{:value}原始值；支持代入{:data.xxx}所有数据中的某项.
         */
        public string $value = ''
    ) {
    }
}
