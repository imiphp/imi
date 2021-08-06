<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Contract;

use Imi\Server\TcpServer\Contract\ITcpServer;

interface ISwooleTcpServer extends ISwooleServer, ITcpServer
{
    /**
     * 是否同步连接.
     */
    public function isSyncConnect(): bool;
}
