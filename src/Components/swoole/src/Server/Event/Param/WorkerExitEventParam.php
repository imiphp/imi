<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Contract\ISwooleServer;

class WorkerExitEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ISwooleServer $server;

    /**
     * Worker进程ID.
     */
    public int $workerId = 0;
}
