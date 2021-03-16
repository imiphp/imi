<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 多对多，中间表模型关联到右侧表.
 *
 * @Annotation
 * @Target("PROPERTY")
 * @Parser("Imi\Bean\Parser\NullParser")
 */
#[\Attribute]
class JoinFromMiddle extends Base
{
    /**
     * 字段名.
     */
    public ?string $field = null;

    /**
     * 中间表模型字段.
     */
    public ?string $middleField = null;

    public function __construct(?array $__data = null, ?string $field = null, ?string $middleField = null)
    {
        parent::__construct(...\func_get_args());
    }
}
