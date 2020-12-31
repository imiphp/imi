<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Parser;

/**
 * 多态多对多关联.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\RelationParser")
 */
class PolymorphicManyToMany extends ManyToMany
{
    /**
     * 右表多态类型字段名.
     *
     * @var string
     */
    public string $type;

    /**
     * 右表多态类型字段值
     *
     * @var mixed
     */
    public $typeValue;
}
