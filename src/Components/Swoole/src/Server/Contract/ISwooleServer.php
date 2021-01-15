<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Contract;

use Imi\Server\Contract\IServer;
use Imi\Swoole\Server\Group\Contract\IServerGroup;
use Swoole\Server;
use Swoole\Server\Port;

interface ISwooleServer extends IServer, IServerGroup
{
    /**
     * 获取 swoole 服务器对象
     *
     * @return \Swoole\Server
     */
    public function getSwooleServer(): Server;

    /**
     * 获取 swoole 监听端口.
     *
     * @return \Swoole\Server\Port
     */
    public function getSwoolePort(): Port;

    /**
     * 是否为子服务器.
     *
     * @return bool
     */
    public function isSubServer(): bool;
}
