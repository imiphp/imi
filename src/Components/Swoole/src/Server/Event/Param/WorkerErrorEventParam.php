<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Base;

class WorkerErrorEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Swoole\Server\Base
     */
    public Base $server;

    /**
     * Worker进程ID.
     *
     * @var int
     */
    public int $workerId;

    /**
     * Worker进程PID.
     *
     * @var int
     */
    public int $workerPid;

    /**
     * 退出的状态码，范围是 1 ～255.
     *
     * @var int
     */
    public int $exitCode;

    /**
     * 进程退出的信号.
     *
     * @var int
     */
    public int $signal;
}
