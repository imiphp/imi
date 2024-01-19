<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;

class AfterSelectEventParam extends CommonEvent
{
    public function __construct(string $__eventName,
        /**
         * 查询结果.
         *
         * @var \Imi\Model\Model[]
         */
        public array $result
    ) {
        parent::__construct($__eventName);
    }
}
