<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Server\Contract\ISwooleServer;

class FinishEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 服务器对象
         */
        public readonly ?ISwooleServer $server = null,
        /**
         * 任务ID.
         */
        public readonly int $taskId = 0,
        /**
         * 任务数据.
         */
        public readonly mixed $data = null,
    ) {
        parent::__construct(SwooleEvents::SERVER_FINISH, $server);
    }
}
