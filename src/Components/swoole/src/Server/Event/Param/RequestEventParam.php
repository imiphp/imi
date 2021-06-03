<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;

class RequestEventParam extends EventParam
{
    /**
     * swoole 请求对象
     */
    public IHttpRequest $request;

    /**
     * swoole 响应对象
     */
    public IHttpResponse $response;
}
