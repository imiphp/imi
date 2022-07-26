<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Contract\ISwooleServer;

class WorkerErrorEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ?ISwooleServer $server = null;

    /**
     * Worker进程ID.
     */
    public int $workerId = 0;

    /**
     * Worker进程PID.
     */
    public int $workerPid = 0;

    /**
     * 退出的状态码，范围是 1 ～255.
     */
    public int $exitCode = 0;

    /**
     * 进程退出的信号.
     */
    public int $signal = 0;
}
