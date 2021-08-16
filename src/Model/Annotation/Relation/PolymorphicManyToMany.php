<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 多态多对多关联.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string $type      右表多态类型字段名
 * @property mixed  $typeValue 右表多态类型字段值
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PolymorphicManyToMany extends RelationBase
{
    /**
     * @param mixed $typeValue
     */
    public function __construct(?array $__data = null, string $model = '', string $middle = '', string $rightMany = '', ?string $order = null, ?array $fields = null, string $type = '', $typeValue = null)
    {
        parent::__construct(...\func_get_args());
    }
}
