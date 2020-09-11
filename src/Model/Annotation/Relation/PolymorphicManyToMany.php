<?php

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 多态多对多关联.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\RelationParser")
 */
class PolymorphicManyToMany extends RelationBase
{
    /**
     * 关联的模型类
     * 可以是包含命名空间的完整类名
     * 可以同命名空间下的类名.
     *
     * @var string
     */
    public $model;

    /**
     * 中间表模型
     * 可以是包含命名空间的完整类名
     * 可以同命名空间下的类名.
     *
     * @var string
     */
    public $middle;

    /**
     * 属性名，赋值为关联的模型对象列表.
     *
     * @var string
     */
    public $rightMany;

    /**
     * 右表多态类型字段名.
     *
     * @var string
     */
    public $type;

    /**
     * 右表多态类型字段值
     *
     * @var mixed
     */
    public $typeValue;

    /**
     * 排序规则字符串.
     *
     * 例：age desc, id desc
     *
     * @var string
     */
    public $order;
}
