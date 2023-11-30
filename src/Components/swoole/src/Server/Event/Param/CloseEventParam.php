<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Server\Contract\ISwooleServer;

class CloseEventParam extends CommonEvent
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
         * 来自那个reactor线程.
         */
        public readonly int $reactorId = 0
    ) {
        parent::__construct('close', $server);
    }
}
