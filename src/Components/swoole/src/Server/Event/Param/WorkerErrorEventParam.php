<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Server\Contract\ISwooleServer;

class WorkerErrorEventParam extends CommonEvent
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

        /**
         * Worker进程PID.
         */
        public readonly int $workerPid = 0,

        /**
         * 退出的状态码，范围是 1 ～255.
         */
        public readonly int $exitCode = 0,

        /**
         * 进程退出的信号.
         */
        public readonly int $signal = 0
    ) {
        parent::__construct(SwooleEvents::SERVER_WORKER_ERROR, $server);
    }
}
