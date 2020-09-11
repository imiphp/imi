<?php

namespace Imi\Server\Http\Error;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Stream\MemoryStream;

/**
 * 执行超时处理器.
 *
 * @Bean("ExecuteTimeoutHandler")
 */
class ExecuteTimeoutHandler implements IExecuteTimeoutHandler
{
    public function handle(Request $request, Response $response)
    {
        $context = RequestContext::getContext();
        $response->withStatus(StatusCode::INTERNAL_SERVER_ERROR)->withBody(new MemoryStream('<h1>Request execute timeout</h1>'))->send();
        $server = $context['server']->getSwooleServer();
        $server->close($context['swooleRequest']->fd);
    }
}
