<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Contract;

use Imi\Server\UdpServer\Contract\IUdpServer;

interface ISwooleUdpServer extends ISwooleServer, IUdpServer
{
}
