<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 多对多，中间表模型关联到右侧表.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class JoinFromMiddle extends Base
{
    public function __construct(
        /**
         * 字段名.
         */
        public ?string $field = null,
        /**
         * 中间表模型字段.
         */
        public ?string $middleField = null
    ) {
    }
}
