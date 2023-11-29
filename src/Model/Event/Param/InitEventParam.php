<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class InitEventParam extends EventParam
{
    /**
     * 模型.
     */
    public ?\Imi\Model\BaseModel $model = null;

    /**
     * 初始化数据.
     */
    public array $data;
}
