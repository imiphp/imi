<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Contract;

use Imi\Server\Contract\IServer;
use Imi\Server\Group\Contract\IServerGroup;
use Swoole\Server;
use Swoole\Server\Port;

interface ISwooleServer extends IServer, IServerGroup
{
    /**
     * 获取 swoole 服务器对象
     */
    public function getSwooleServer(): Server;

    /**
     * 获取 swoole 监听端口.
     */
    public function getSwoolePort(): Port;

    /**
     * 是否为子服务器.
     */
    public function isSubServer(): bool;
}
