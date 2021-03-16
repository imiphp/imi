<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Contract;

use Imi\Server\Contract\IServer;
use Imi\Server\Group\Contract\IServerGroup;

interface IWebSocketServer extends IServer, IServerGroup
{
    /**
     * 向客户端推送消息.
     */
    public function push(int $fd, string $data, int $opcode = 1): bool;
}
