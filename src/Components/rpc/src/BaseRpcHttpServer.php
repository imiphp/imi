<?php

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Server\Http\Server;

/**
 * RPC Http 服务器基类.
 */
abstract class BaseRpcHttpServer extends Server implements IRpcServer
{
}
