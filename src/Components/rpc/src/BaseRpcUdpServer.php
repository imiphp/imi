<?php

declare(strict_types=1);

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Swoole\Server\UdpServer\Server;

/**
 * RPC UDP 服务器基类.
 */
abstract class BaseRpcUdpServer extends Server implements IRpcServer
{
}
