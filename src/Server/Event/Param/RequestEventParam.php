<?php

namespace Imi\Server\Event\Param;

use Imi\Event\EventParam;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;

class RequestEventParam extends EventParam
{
    /**
     * swoole 请求对象
     *
     * @var \Imi\Server\Http\Message\Contract\IHttpRequest
     */
    public IHttpRequest $request;

    /**
     * swoole 响应对象
     *
     * @var \Imi\Server\Http\Message\Contract\IHttpResponse
     */
    public IHttpResponse $response;
}
