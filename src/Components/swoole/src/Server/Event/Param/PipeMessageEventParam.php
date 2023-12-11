<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Server\Contract\ISwooleServer;

class PipeMessageEventParam extends CommonEvent
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
         * 消息内容，可以是任意PHP类型.
         */
        public readonly mixed $message = null
    ) {
        parent::__construct(SwooleEvents::SERVER_PIPE_MESSAGE, $server);
    }
}
