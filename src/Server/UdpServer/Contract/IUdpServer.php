<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Contract;

use Imi\Server\Contract\IServer;
use Imi\Server\Group\Contract\IServerGroup;

interface IUdpServer extends IServer, IServerGroup
{
    /**
     * 向客户端发送消息.
     *
     * @param string $ip
     * @param int    $port
     * @param string $data
     *
     * @return bool
     */
    public function sendTo(string $ip, int $port, string $data): bool;
}
