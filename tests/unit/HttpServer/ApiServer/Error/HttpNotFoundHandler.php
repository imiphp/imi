<?php

namespace Imi\Test\HttpServer\ApiServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Error\IHttpNotFoundHandler;
use Imi\Util\Stream\MemoryStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("MyHttpNotFoundHandler")
 */
class HttpNotFoundHandler implements IHttpNotFoundHandler
{
    public function handle(RequestHandlerInterface $requesthandler, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response->withBody(new MemoryStream('gg'));
    }
}
