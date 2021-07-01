<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 提取字段中的属性到当前模型.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string $fieldName 字段名，支持.的形式无限级取值
 * @property string $alias     提取到当前模型中的字段别名，不设置默认为原始字段名
 */
#[\Attribute]
class ExtractProperty extends Base
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'fieldName';

    public function __construct(?array $__data = null, string $fieldName = '', string $alias = '')
    {
        parent::__construct(...\func_get_args());
    }
}
