<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeBatchUpdateEventParam extends EventParam
{
    /**
     * 初始化数据.
     *
     * @var \Imi\Util\LazyArrayObject
     */
    public $data;

    /**
     * 查询器.
     *
     * @var \Imi\Db\Query\Interfaces\IQuery
     */
    public $query;
}
