<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Model\Enum\RelationPoolName;

/**
 * 自定义属性关联.
 *
 * @Annotation
 *
 * @Target("PROPERTY")
 *
 * @property bool          $with       关联预加载查询
 * @property string[]|null $withFields 设置结果模型的序列化字段
 * @property int|string    $poolName   连接池名称，或 \Imi\Model\Enum\RelationPoolName 中的常量
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation extends RelationBase
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'with';

    /**
     * @param int|string|null $poolName 连接池名称，或 \Imi\Model\Enum\RelationPoolName 中的常量
     */
    public function __construct(?array $__data = null, bool $with = true, ?array $withFields = null, $poolName = RelationPoolName::PARENT)
    {
        parent::__construct(...\func_get_args());
    }
}
