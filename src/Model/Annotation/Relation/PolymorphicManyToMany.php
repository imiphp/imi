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
 */
#[\Attribute]
class PolymorphicManyToMany extends ManyToMany
{
    /**
     * 右表多态类型字段名.
     */
    public string $type = '';

    /**
     * 右表多态类型字段值
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

    public function __construct(?array $__data = null, string $type = '', ?array $fields = null)
    {
        parent::__construct(...\func_get_args());
    }
}
