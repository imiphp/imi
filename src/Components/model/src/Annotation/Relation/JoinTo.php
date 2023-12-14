<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 关联右侧字段.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class JoinTo extends Base
{
    public function __construct(
        /**
         * 字段名.
         */
        public ?string $field = null
    ) {
    }
}
