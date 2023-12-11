<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Server\Contract\ISwooleServer;

class PacketEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 服务器对象
         */
        public readonly ?ISwooleServer $server = null,

        /**
         * 数据.
         */
        public readonly string $data = '',

        /**
         * 客户端信息.
         */
        public readonly array $clientInfo = []
    ) {
        parent::__construct('packet', $server);
    }
}
