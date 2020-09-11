<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterDeleteEventParam extends EventParam
{
    /**
     * 模型.
     *
     * @var \Imi\Model\BaseModel
     */
    public $model;

    /**
     * 查询结果.
     *
     * @var \Imi\Db\Query\Interfaces\IResult
     */
    public $result;
}
