<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Model\Event\ModelEvents;

class AfterQueryEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 模型.
         */
        public ?\Imi\Model\BaseModel $model = null
    ) {
        parent::__construct(ModelEvents::AFTER_QUERY, $model);
    }
}
