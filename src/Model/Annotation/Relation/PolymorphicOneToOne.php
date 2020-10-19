<?php

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 多态一对一
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\RelationParser")
 */
class PolymorphicOneToOne extends RelationBase
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'model';

    /**
     * 关联的模型类
     * 可以是包含命名空间的完整类名
     * 可以同命名空间下的类名.
     *
     * @var string
     */
    public $model;

    /**
     * 多态类型字段名.
     *
     * @var string
     */
    public $type;

    /**
     * 多态类型字段值
     *
     * @var mixed
     */
    public $typeValue;
}
