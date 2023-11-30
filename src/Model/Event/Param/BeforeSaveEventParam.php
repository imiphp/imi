<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Model\Event\ModelEvents;

class BeforeSaveEventParam extends CommonEvent
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
         * 查询器.
         */
        public readonly \Imi\Db\Query\Interfaces\IQuery $query
    ) {
        parent::__construct(ModelEvents::BEFORE_SAVE, $model);
    }
}
