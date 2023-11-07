<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Contract\ISwooleServer;

class ReceiveEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ?ISwooleServer $server = null;

    /**
     * 客户端连接的标识符.
     */
    public int|string $clientId = 0;

    /**
     * Reactor线程ID.
     */
    public int $reactorId = 0;

    /**
     * 接收到的数据.
     */
    public string $data = '';
}
