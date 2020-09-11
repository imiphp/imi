<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeFindEventParam extends EventParam
{
    /**
     * 主键值们.
     *
     * @var array
     */
    public $ids;

    /**
     * 查询器.
     *
     * @var \Imi\Db\Query\Interfaces\IQuery
     */
    public $query;
}
