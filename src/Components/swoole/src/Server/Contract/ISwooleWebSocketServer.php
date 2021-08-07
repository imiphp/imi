<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Contract;

use Imi\Server\WebSocket\Contract\IWebSocketServer;

interface ISwooleWebSocketServer extends ISwooleServer, IWebSocketServer
{
}
