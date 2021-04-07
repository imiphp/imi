<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Contract;

use Imi\Server\Contract\IServer;
use Imi\Server\Group\Contract\IServerGroup;

interface ITcpServer extends IServer, IServerGroup
{
    /**
     * 向客户端发送消息.
     *
     * @param int|string $clientId
     */
    public function send($clientId, string $data): bool;
}
