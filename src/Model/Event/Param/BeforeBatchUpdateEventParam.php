<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeBatchUpdateEventParam extends EventParam
{
    /**
     * 初始化数据.
     */
    public \Imi\Util\LazyArrayObject $data;

    /**
     * 查询器.
     */
    public \Imi\Db\Query\Interfaces\IQuery $query;
}
