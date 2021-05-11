<?php

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Server\TcpServer\Server;

/**
 * RPC TCP 服务器基类.
 */
abstract class BaseRpcTcpServer extends Server implements IRpcServer
{
}
