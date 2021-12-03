<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;

/**
 * 验证时的值
 *
 * @Annotation
 * @Target({"ANNOTATION"})
 *
 * @property string $value 值规则；支持代入{:value}原始值；支持代入{:data.xxx}所有数据中的某项
 */
#[\Attribute]
class ValidateValue extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'value';

    public function __construct(?array $__data = null, string $value = '')
    {
        parent::__construct(...\func_get_args());
    }
}
