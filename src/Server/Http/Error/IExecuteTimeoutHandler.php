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
    public function handle(IHttpRequest $request, IHttpResponse $response);
}
