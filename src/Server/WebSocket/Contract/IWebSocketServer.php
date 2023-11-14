<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Contract;

use Imi\Server\Contract\IServer;
use Imi\Server\Group\Contract\IServerGroup;
use Imi\Server\WebSocket\Enum\NonControlFrameType;

interface IWebSocketServer extends IServer, IServerGroup
{
    /**
     * 向客户端推送消息.
     */
    public function push(int|string $clientId, string $data, int $opcode = 1): bool;

    /**
     * 非控制帧类型.
     */
    public function getNonControlFrameType(): NonControlFrameType;
}
