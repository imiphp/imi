<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterSelectEventParam extends EventParam
{
    /**
     * 查询结果.
     *
     * @var \Imi\Model\BaseModel[]
     */
    public $result;
}
