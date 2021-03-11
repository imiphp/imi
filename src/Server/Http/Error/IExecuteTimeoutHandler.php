<?php

namespace Imi\Server\Http\Error;

use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;

/**
 * 执行超时接口.
 */
interface IExecuteTimeoutHandler
{
    /**
     * @param \Imi\Server\Http\Message\Request  $request
     * @param \Imi\Server\Http\Message\Response $response
     *
     * @return mixed
     */
    public function handle(Request $request, Response $response);
}
