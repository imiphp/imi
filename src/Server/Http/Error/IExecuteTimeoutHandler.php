<?php

declare(strict_types=1);

namespace Imi\Server\Http\Error;

use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;

/**
 * 执行超时接口.
 */
interface IExecuteTimeoutHandler
{
    /**
     * @param \Imi\Server\Http\Message\Contract\IHttpRequest  $request
     * @param \Imi\Server\Http\Message\Contract\IHttpResponse $response
     *
     * @return mixed
     */
    public function handle(IHttpRequest $request, IHttpResponse $response);
}
