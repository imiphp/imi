<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 多态一对一
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class PolymorphicOneToOne extends RelationBase
{
    /**
     * 只传一个参数时的参数名.
     */
    protected ?string $defaultFieldName = 'model';

    /**
     * 关联的模型类
     * 可以是包含命名空间的完整类名
     * 可以同命名空间下的类名.
     */
    public string $model = '';

    /**
     * 多态类型字段名.
     */
    public string $type = '';

    /**
     * 多态类型字段值
     *
     * @var mixed
     */
    public $typeValue;

    /**
     * 为查询出来的模型指定字段.
     *
     * @var string[]|null
     */
    public ?array $fields = null;

    public function __construct(?array $__data = null, string $model = '', string $type = '', ?array $fields = null)
    {
        parent::__construct(...\func_get_args());
    }
}
