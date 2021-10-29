<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 多态一对多.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string        $model     关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名
 * @property string        $type      多态类型字段名
 * @property mixed         $typeValue 多态类型字段值
 * @property string|null   $order     排序规则字符串；例：age desc, id desc
 * @property string[]|null $fields    为查询出来的模型指定字段
 * @property int|null      $limit     限制返回记录数量
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
    public function __construct(?array $__data = null, string $model = '', string $type = '', $typeValue = null, ?string $order = null, ?array $fields = null, ?int $limit = null, bool $with = false)
    {
        parent::__construct(...\func_get_args());
    }
}
