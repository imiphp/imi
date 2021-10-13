<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\Middleware;

use Imi\Log\Log;
use Imi\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestLogMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Log::info('Server: ' . RequestContext::getServer()->getName() . ', Url: ' . $request->getUri());

        return $handler->handle($request);
    }
}
