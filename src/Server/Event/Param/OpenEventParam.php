<?php

declare(strict_types=1);

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Base;
use Imi\Server\Http\Message\Contract\IHttpRequest;

class OpenEventParam extends EventParam
{
    /**
     * 服务器对象
     *
     * @var \Imi\Server\Base
     */
    public Base $server;

    /**
     * 客户端连接的标识符.
     *
     * @var \Imi\Server\Http\Message\Contract\IHttpRequest
     */
    public IHttpRequest $request;
}
