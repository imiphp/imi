<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterQueryEventParam extends EventParam
{
    /**
     * 模型.
     */
    public ?\Imi\Model\BaseModel $model;
}
