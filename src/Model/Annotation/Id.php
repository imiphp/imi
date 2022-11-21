<?php

declare(strict_types=1);

namespace Imi\Model\Annotation;

use Imi\Bean\Annotation\Base;

/**
 * 表主键声明.
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 *
 * @property int|false|null $index            顺序。默认为 null 时以属性顺序为准；为 false 时表示不是主键，但 ID 生成器依然有效。
 * @property string         $generator        ID 生成器类名，如果是空字符串则不自动生成
 * @property array          $generatorOptions
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Id extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'index';

    /**
     * @param int|false|null $index
     */
    public function __construct(?array $__data = null, $index = null, string $generator = '', array $generatorOptions = [])
    {
        parent::__construct(...\func_get_args());
    }
}
