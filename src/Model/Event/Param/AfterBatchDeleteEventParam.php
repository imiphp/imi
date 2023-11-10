<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterBatchDeleteEventParam extends EventParam
{
    /**
     * 查询结果.
     */
    public \Imi\Db\Query\Interfaces\IResult $result;
}
