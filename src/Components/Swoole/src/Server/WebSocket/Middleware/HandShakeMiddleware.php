<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Swoole\Server\Event\Param\OpenEventParam;
use Imi\Util\Http\Consts\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("HandShakeMiddleware")
 */
class HandShakeMiddleware implements MiddlewareInterface
{
    /**
     * 处理方法.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var IHttpResponse $response */
        $response = $handler->handle($request);
        if ('websocket' !== $request->getHeaderLine('Upgrade'))
        {
            /** @var \Imi\Server\Http\Route\RouteResult $routeResult */
            $routeResult = RequestContext::get('routeResult');
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
            $routeResult = RequestContext::get('routeResult');
            $routeResult->routeItem->callable = null;
            $routeResult->callable = null;
            ConnectContext::muiltiSet([
                'httpRouteResult'   => $routeResult,
                'uri'               => $request->getUri(),
            ]);

            $server = RequestContext::get('server');
            $server->trigger('open', [
                'server'   => &$server,
                'request'  => &$request,
            ], $this, OpenEventParam::class);
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
            $response = $response->setHeader($key, $val);
        }

        $response = $response->setStatus(StatusCode::SWITCHING_PROTOCOLS);

        return $response;
    }
}
