<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

/**
 * 自定义属性关联.
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 *
 * @property bool          $with       关联预加载查询
 * @property string[]|null $withFields 设置结果模型的序列化字段
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation extends RelationBase
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'with';

    public function __construct(?array $__data = null, bool $with = true, ?array $withFields = null)
    {
        parent::__construct(...\func_get_args());
    }
}
