<?php

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Server\WebSocket\Server;

/**
 * RPC WebSocket 服务器基类.
 */
abstract class BaseRpcWebSocketServer extends Server implements IRpcServer
{
}
