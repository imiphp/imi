<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

/**
 * 多态多对多关联.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @property string        $type           右表多态类型字段名
 * @property mixed         $typeValue      右表多态类型字段值
 * @property int|null      $limit          限制返回记录数量
 * @property bool          $with           关联预加载查询
 * @property string[]|null $withFields     设置结果模型的序列化字段
 * @property bool          $withSoftDelete 查询结果是否包含被软删除的数据，仅查询有效。非软删除模型请勿设置为 true
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PolymorphicManyToMany extends ManyToMany
{
    /**
     * @param mixed $typeValue
     */
    public function __construct(?array $__data = null, string $model = '', string $middle = '', string $rightMany = '', ?string $order = null, ?array $fields = null, string $type = '', $typeValue = null, ?int $limit = null, bool $with = false, ?array $withFields = null, bool $withSoftDelete = false)
    {
        parent::__construct(...\func_get_args());
    }
}
