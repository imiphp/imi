<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\Server;
use Imi\Swoole\Server\Event\Param\OpenEventParam;
use Imi\Util\Http\Consts\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoole\Coroutine;

/**
 * @Bean(name="HandShakeMiddleware", env="swoole")
 */
class HandShakeMiddleware implements MiddlewareInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var IHttpResponse $response */
        $response = $handler->handle($request);
        $requestContext = RequestContext::getContext();
        if ('websocket' !== $request->getHeaderLine('Upgrade'))
        {
            /** @var \Imi\Server\Http\Route\RouteResult $routeResult */
            $routeResult = $requestContext['routeResult'] ?? null;
            if ($routeResult->routeItem->wsConfig['wsOnly'] ?? false)
            {
                $response = $response->setStatus(StatusCode::BAD_REQUEST);
            }

            return $response;
        }
        if (StatusCode::OK === $response->getStatusCode() && 'Upgrade' !== $response->getHeaderLine('Connection'))
        {
            // 未做处理则做默认握手处理
            // @phpstan-ignore-next-line
            $response = $this->defaultHandShake($request, $response);
        }
        if (StatusCode::SWITCHING_PROTOCOLS === $response->getStatusCode())
        {
            // http 路由解析结果
            /** @var \Imi\Server\Http\Route\RouteResult $routeResult */
            $routeResult = $requestContext['routeResult'] ?? null;
            ConnectionContext::muiltiSet([
                'dataParser' => $routeResult->routeItem->wsConfig->parserClass ?? null,
                'uri'        => (string) $request->getUri(),
            ]);

            $server = $requestContext['server'];
            $server->trigger('open', [
                'server'   => &$server,
                'request'  => &$request,
            ], $this, OpenEventParam::class);
        }
        else
        {
            $clientId = $requestContext['clientId'];
            Coroutine::defer(static function () use ($clientId) {
                Server::close($clientId);
            });
        }

        return $response;
    }

    /**
     * 默认握手处理.
     */
    private function defaultHandShake(IHttpRequest $request, IHttpResponse $response): ?IHttpResponse
    {
        $secWebSocketKey = $request->getHeaderLine('sec-websocket-key');
        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $secWebSocketKey) || 16 !== \strlen(base64_decode($secWebSocketKey)))
        {
            return null;
        }

        $key = base64_encode(sha1($secWebSocketKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

        $headers = [
            'Upgrade'               => 'websocket',
            'Connection'            => 'Upgrade',
            'Sec-WebSocket-Accept'  => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        if ($request->hasHeader('Sec-WebSocket-Protocol'))
        {
            $headers['Sec-WebSocket-Protocol'] = $request->getHeaderLine('Sec-WebSocket-Protocol');
        }

        foreach ($headers as $key => $val)
        {
            $response = $response->setHeader((string) $key, $val);
        }

        return $response->setStatus(StatusCode::SWITCHING_PROTOCOLS);
    }
}
