<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class FinishEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * 任务ID.
     *
     * @var int
     */
    public $taskID;

    /**
     * 任务数据.
     *
     * @var mixed
     */
    public $data;
}
