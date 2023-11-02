<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 提取字段中的属性到当前模型.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class ExtractProperty extends Base
{
    public function __construct(
        /**
         * 字段名，支持.的形式无限级取值
         */
        public string $fieldName = '',
        /**
         * 提取到当前模型中的字段别名，不设置默认为原始字段名.
         */
        public string $alias = ''
    ) {
    }
}
