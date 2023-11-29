<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterFindEventParam extends EventParam
{
    /**
     * 主键值们.
     */
    public array $ids;

    /**
     * 模型.
     */
    public ?\Imi\Model\BaseModel $model = null;
}
