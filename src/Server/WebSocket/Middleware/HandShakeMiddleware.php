<?php
namespace Imi\Server\WebSocket\Middleware;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Http\Consts\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Imi\Server\Event\Param\OpenEventParam;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("HandShakeMiddleware")
 */
class HandShakeMiddleware implements MiddlewareInterface
{
    /**
     * 处理方法
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if('websocket' !== $request->getHeaderLine('Upgrade'))
        {
            /** @var \Imi\Server\Http\Route\RouteResult $routeResult */
            $routeResult = RequestContext::get('routeResult');
            if($routeResult->routeItem->wsConfig['wsOnly'] ?? false)
            {
                $response = $response->withStatus(StatusCode::BAD_REQUEST);
            }
            return $response;
        }
        if(StatusCode::OK === $response->getStatusCode() && 'Upgrade' !== $response->getHeaderLine('Connection'))
        {
            // 未做处理则做默认握手处理
            $response = $this->defaultHandShake($request, $response);
        }
        if(StatusCode::SWITCHING_PROTOCOLS === $response->getStatusCode())
        {
            // http 路由解析结果
            $routeResult = RequestContext::get('routeResult');
            $routeResult->routeItem->callable = null;
            $routeResult->callable = null;
            ConnectContext::muiltiSet([
                'httpRouteResult'   =>  $routeResult,
                'uri'               =>  $request->getUri(),
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
     * 默认握手处理
     *
     * @param \Imi\Server\Http\Message\Request $request
     * @param \Imi\Server\Http\Message\Response $response
     * @return void
     */
    private function defaultHandShake($request, $response)
    {
        $secWebSocketKey = $request->getHeaderLine('sec-websocket-key');
        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey)))
        {
            return;
        }

        $key = base64_encode(sha1($secWebSocketKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

        $headers = [
            'Upgrade'               => 'websocket',
            'Connection'            => 'Upgrade',
            'Sec-WebSocket-Accept'  => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        if($request->hasHeader('Sec-WebSocket-Protocol'))
        {
            $headers['Sec-WebSocket-Protocol'] = $request->getHeaderLine('Sec-WebSocket-Protocol');
        }

        foreach ($headers as $key => $val)
        {
            $response = $response->withHeader($key, $val);
        }

        $response = $response->withStatus(StatusCode::SWITCHING_PROTOCOLS);
        
        return $response;
    }
}