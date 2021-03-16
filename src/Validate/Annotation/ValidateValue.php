<?php

declare(strict_types=1);

namespace Imi\Validate\Annotation;

use Imi\Bean\Annotation;
use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 验证时的值
 *
 * @Annotation
 * @Target({"ANNOTATION"})
 * @Parser("\Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class ValidateValue extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'value';

    /**
     * 值规则.
     *
     * 支持代入{:value}原始值
     * 支持代入{:data.xxx}所有数据中的某项
     */
    public string $value = '';

    public function __construct(?array $__data = null, string $value = '')
    {
        parent::__construct(...\func_get_args());
    }
}
