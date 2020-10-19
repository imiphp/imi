<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;

class WorkerErrorEventParam extends EventParam
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

    /**
     * Worker进程PID.
     *
     * @var int
     */
    public $workerPid;

    /**
     * 退出的状态码，范围是 1 ～255.
     *
     * @var int
     */
    public $exitCode;

    /**
     * 进程退出的信号.
     *
     * @var int
     */
    public $signal;
}
