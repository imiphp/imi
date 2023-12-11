<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Model\Event\ModelEvents;

class AfterSaveEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 模型.
         */
        public readonly ?\Imi\Model\BaseModel $model,

        /**
         * 初始化数据.
         */
        public readonly \Imi\Util\LazyArrayObject $data,

        /**
         * 查询结果.
         */
        public readonly \Imi\Db\Query\Interfaces\IResult $result
    ) {
        parent::__construct(ModelEvents::AFTER_SAVE, $model);
    }
}
