<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeBatchDeleteEventParam extends EventParam
{
    /**
     * 查询器.
     */
    public \Imi\Db\Query\Interfaces\IQuery $query;
}
