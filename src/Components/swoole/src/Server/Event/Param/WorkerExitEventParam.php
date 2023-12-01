<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Server\Contract\ISwooleServer;

class WorkerExitEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 服务器对象
         */
        public readonly ?ISwooleServer $server = null,
        /**
         * Worker进程ID.
         */
        public readonly int $workerId = 0,
    ) {
        parent::__construct(SwooleEvents::SERVER_WORKER_EXIT);
    }
}
