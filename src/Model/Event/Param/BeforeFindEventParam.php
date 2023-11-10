<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeFindEventParam extends EventParam
{
    /**
     * 主键值们.
     */
    public array $ids;

    /**
     * 查询器.
     */
    public \Imi\Db\Query\Interfaces\IQuery $query;
}
