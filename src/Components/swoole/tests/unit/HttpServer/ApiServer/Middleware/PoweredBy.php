<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 增加一个响应头，仅作演示，生产环境请去除.
 */
class PoweredBy implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // @phpstan-ignore-next-line
        return $handler->handle($request)->setHeader('X-Powered-By', 'imiphp.com');
    }
}
