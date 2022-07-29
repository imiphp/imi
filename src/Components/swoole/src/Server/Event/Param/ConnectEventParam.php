<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Swoole\Server\Contract\ISwooleServer;

class ConnectEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ?ISwooleServer $server = null;

    /**
     * 客户端连接的标识符.
     *
     * @var int|string
     */
    public $clientId = 0;

    /**
     * Reactor线程ID.
     */
    public int $reactorId = 0;
}
