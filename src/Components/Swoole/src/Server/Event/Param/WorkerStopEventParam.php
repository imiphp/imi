<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Base;

class WorkerStopEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public Base $server;

    /**
     * Worker进程ID.
     */
    public int $workerId = 0;
}
