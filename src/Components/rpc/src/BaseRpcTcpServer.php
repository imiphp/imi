<?php

declare(strict_types=1);

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Swoole\Server\TcpServer\Server;

/**
 * RPC TCP 服务器基类.
 */
abstract class BaseRpcTcpServer extends Server implements IRpcServer
{
}
