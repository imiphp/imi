<?php

declare(strict_types=1);

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Base;

class FinishEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public Base $server;

    /**
     * 任务ID.
     *
     * @var int
     */
    public int $taskId;

    /**
     * 任务数据.
     *
     * @var mixed
     */
    public $data;
}
