<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\HttpServer\ApiServer\Error;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Error\IHttpNotFoundHandler;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Util\Stream\MemoryStream;
use Psr\Http\Server\RequestHandlerInterface;

#[Bean(name: 'MyHttpNotFoundHandler')]
class HttpNotFoundHandler implements IHttpNotFoundHandler
{
    /**
     * {@inheritDoc}
     */
    public function handle(RequestHandlerInterface $requesthandler, IHttpRequest $request, IHttpResponse $response): IHttpResponse
    {
        return $response->withBody(new MemoryStream('gg'));
    }
}
