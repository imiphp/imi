<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterDeleteEventParam extends EventParam
{
    /**
     * 模型.
     */
    public \Imi\Model\BaseModel $model;

    /**
     * 查询结果.
     */
    public \Imi\Db\Query\Interfaces\IResult $result;
}
