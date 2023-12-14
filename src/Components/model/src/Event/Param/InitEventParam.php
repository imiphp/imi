<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;

class InitEventParam extends CommonEvent
{
    public function __construct(string $__eventName,
        /**
         * 模型.
         */
        public readonly ?\Imi\Model\BaseModel $model,

        /**
         * 初始化数据.
         */
        public readonly array $data
    ) {
        parent::__construct($__eventName, $model);
    }
}
