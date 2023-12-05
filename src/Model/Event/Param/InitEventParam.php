<?php

declare(strict_types=1);

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class InitEventParam extends EventParam
{
    /**
     * 模型.
     *
     * @var \Imi\Model\BaseModel|null
     */
    public $model;

    /**
     * 初始化数据.
     *
     * @var array
     */
    public $data;
}
