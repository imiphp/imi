<?php

declare(strict_types=1);

namespace Imi\Rpc;

use Imi\Rpc\Contract\IRpcServer;
use Imi\Swoole\Server\Base;

/**
 * RPC 服务器基类.
 */
abstract class BaseRpcServer extends Base implements IRpcServer
{
}
