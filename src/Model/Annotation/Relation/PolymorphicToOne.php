<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 用于多态一对一、一对多关联被关联的模型中使用，查询对应的左侧模型.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 *
 * @property string        $model      关联的模型类；可以是包含命名空间的完整类名；可以同命名空间下的类名
 * @property string        $modelField 关联的模型用于关联的字段
 * @property string        $field      当前模型用于关联的字段
 * @property string        $type       多态类型字段名
 * @property mixed         $typeValue  多态类型字段值
 * @property string[]|null $fields     为查询出来的模型指定字段
 * @property bool          $with       关联预加载查询
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PolymorphicToOne extends RelationBase
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'model';

    /**
     * @param mixed $typeValue
     */
    public function __construct(?array $__data = null, string $model = '', string $modelField = '', string $field = '', string $type = '', $typeValue = null, ?array $fields = null, bool $with = false)
    {
        parent::__construct(...\func_get_args());
    }
}
