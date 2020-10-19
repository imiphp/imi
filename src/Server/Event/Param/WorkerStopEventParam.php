<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class WorkerStopEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public $server;

    /**
     * Worker进程ID.
     *
     * @var int
     */
    public $workerID;
}
