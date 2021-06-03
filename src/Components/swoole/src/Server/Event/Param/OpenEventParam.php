<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Swoole\Server\Contract\ISwooleServer;

class OpenEventParam extends EventParam
{
    /**
     * 服务器对象
     */
    public ISwooleServer $server;

    /**
     * 客户端连接的标识符.
     */
    public IHttpRequest $request;
}
