<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\CommonEvent;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Swoole\WebSocket\Frame;

class MessageEventParam extends CommonEvent
{
    public function __construct(
        /**
         * 服务器对象
         */
        public readonly ?ISwooleServer $server = null,

        /**
         * swoole 数据帧对象
         */
        public readonly ?Frame $frame = null
    ) {
        parent::__construct('message', $server);
    }
}
