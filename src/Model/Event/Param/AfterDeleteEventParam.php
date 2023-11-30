<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Model\Event\ModelEvents;

class AfterDeleteEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 模型.
         */
        public readonly ?\Imi\Model\BaseModel $model,

        /**
         * 查询结果.
         */
        public \Imi\Db\Query\Interfaces\IResult $result
    ) {
        parent::__construct(ModelEvents::AFTER_SAVE, $model);
    }
}
