<?php

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Server\UdpServer\Server;

/**
 * RPC UDP 服务器基类.
 */
abstract class BaseRpcUdpServer extends Server implements IRpcServer
{
}
