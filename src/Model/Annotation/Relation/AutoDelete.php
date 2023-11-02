<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 自动删除.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AutoDelete extends Base
{
    public function __construct(
        /**
         * 是否开启.
         */
        public bool $status = true
    ) {
    }
}
