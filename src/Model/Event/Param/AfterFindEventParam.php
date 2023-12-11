<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;

class AfterFindEventParam extends CommonEvent
{
    public function __construct(string $__eventName,
        /**
         * 主键值们.
         */
        public readonly array $ids,

        /**
         * 模型.
         */
        public ?\Imi\Model\Model $model = null
    ) {
        parent::__construct($__eventName);
    }
}
