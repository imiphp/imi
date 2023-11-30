<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;

class BeforeFindEventParam extends CommonEvent
{
    public function __construct(string $__eventName,
        /**
         * 主键值们.
         */
        public readonly array $ids,

        /**
         * 查询器.
         */
        public \Imi\Db\Query\Interfaces\IQuery $query
    ) {
        parent::__construct($__eventName);
    }
}
