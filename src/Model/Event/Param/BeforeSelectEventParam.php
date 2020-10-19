<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeSelectEventParam extends EventParam
{
    /**
     * 查询器.
     *
     * @var \Imi\Db\Query\Interfaces\IQuery
     */
    public $query;
}
