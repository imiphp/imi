<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterBatchUpdateEventParam extends EventParam
{
    /**
     * 初始化数据.
     *
     * @var \Imi\Util\LazyArrayObject
     */
    public $data;

    /**
     * 查询结果.
     *
     * @var \Imi\Db\Query\Interfaces\IResult
     */
    public $result;
}
