<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\CommonEvent;

class BeforeSelectEventParam extends CommonEvent
{
    public function __construct(string $__eventName,
        /**
         * 查询器.
         */
        public \Imi\Db\Query\Interfaces\IQuery $query
    ) {
        parent::__construct($__eventName);
    }
}
