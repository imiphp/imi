<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Server\Contract\ISwooleServer;

class ReceiveEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 服务器对象
         */
        public readonly ?ISwooleServer $server = null,

        /**
         * 客户端连接的标识符.
         */
        public readonly int|string $clientId = 0,

        /**
         * Reactor线程ID.
         */
        public readonly int $reactorId = 0,

        /**
         * 接收到的数据.
         */
        public readonly string $data = '',
    ) {
        parent::__construct('receive', $server);
    }
}
