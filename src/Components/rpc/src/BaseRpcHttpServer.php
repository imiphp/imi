<?php

declare(strict_types=1);

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Swoole\Server\Http\Server;

/**
 * RPC Http 服务器基类.
 */
abstract class BaseRpcHttpServer extends Server implements IRpcServer
{
}
