<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeDeleteEventParam extends EventParam
{
    /**
     * 模型.
     *
     * @var \Imi\Model\BaseModel
     */
    public $model;

    /**
     * 查询器.
     *
     * @var \Imi\Db\Query\Interfaces\IQuery
     */
    public $query;
}
