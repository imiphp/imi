<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterBatchDeleteEventParam extends EventParam
{
    /**
     * 查询结果.
     *
     * @var \Imi\Db\Query\Interfaces\IResult
     */
    public $result;
}
