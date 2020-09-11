<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterQueryEventParam extends EventParam
{
    /**
     * 模型.
     *
     * @var \Imi\Model\BaseModel
     */
    public $model;
}
