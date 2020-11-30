<?php

declare(strict_types=1);

namespace Imi\Server\Http\Error;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\Stream\MemoryStream;

/**
 * 执行超时处理器.
 *
 * @Bean("ExecuteTimeoutHandler")
 */
class ExecuteTimeoutHandler implements IExecuteTimeoutHandler
{
    public function handle(IHttpRequest $request, IHttpResponse $response)
    {
        $context = RequestContext::getContext();
        $response->setStatus(StatusCode::INTERNAL_SERVER_ERROR)->setBody(new MemoryStream('<h1>Request execute timeout</h1>'))->send();
        $server = $context['server']->getSwooleServer();
        $server->close($context['swooleRequest']->fd);
    }
}
