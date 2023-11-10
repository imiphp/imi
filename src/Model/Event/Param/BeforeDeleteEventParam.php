<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class BeforeDeleteEventParam extends EventParam
{
    /**
     * 模型.
     */
    public \Imi\Model\BaseModel $model;

    /**
     * 查询器.
     */
    public \Imi\Db\Query\Interfaces\IQuery $query;
}
