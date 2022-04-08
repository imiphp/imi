<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

/**
 * 多态一对多.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @property string        $model          关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名
 * @property string        $type           多态类型字段名
 * @property mixed         $typeValue      多态类型字段值
 * @property string|null   $order          排序规则字符串；例：age desc, id desc
 * @property string[]|null $fields         查询时指定字段
 * @property int|null      $limit          限制返回记录数量
 * @property bool          $with           关联预加载查询
 * @property string[]|null $withFields     设置结果模型的序列化字段
 * @property bool          $withSoftDelete 查询结果是否包含被软删除的数据，仅查询有效。非软删除模型请勿设置为 true
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PolymorphicOneToMany extends RelationBase
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'model';

    /**
     * @param mixed $typeValue
     */
    public function __construct(?array $__data = null, string $model = '', string $type = '', $typeValue = null, ?string $order = null, ?array $fields = null, ?int $limit = null, bool $with = false, ?array $withFields = null, bool $withSoftDelete = false)
    {
        parent::__construct(...\func_get_args());
    }
}
