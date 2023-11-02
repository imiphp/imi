<?php

declare(strict_types=1);

namespace Imi\Model\Annotation\Relation;

use Imi\Bean\Annotation\Base;

/**
 * 自动保存.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AutoSave extends Base
{
    public function __construct(
        /**
         * 是否开启.
         */
        public bool $status = true,
        /**
         * save时，删除无关联数据.
         */
        public bool $orphanRemoval = false
    ) {
    }
}
