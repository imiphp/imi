<?php

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 多对多，左侧关联到中间表模型.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Model\Parser\RelationParser")
 */
class JoinToMiddle extends Base
{
    /**
     * 字段名.
     *
     * @var string
     */
    public $field;

    /**
     * 中间表模型字段.
     *
     * @var string
     */
    public $middleField;
}
