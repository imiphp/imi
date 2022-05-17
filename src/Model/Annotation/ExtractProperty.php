<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 提取字段中的属性到当前模型.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @property string $fieldName 字段名，支持.的形式无限级取值
 * @property string $alias     提取到当前模型中的字段别名，不设置默认为原始字段名
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class ExtractProperty extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'fieldName';

    public function __construct(?array $__data = null, string $fieldName = '', string $alias = '')
    {
        parent::__construct(...\func_get_args());
    }
}
