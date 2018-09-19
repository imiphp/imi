<?php
namespace Imi\Server\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\Util\Http\Consts\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean
 */
class HandShakeMiddleware implements MiddlewareInterface
{
    /**
     * 处理方法
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if(StatusCode::OK === $response->getStatusCode() && 'Upgrade' !== $response->getHeaderLine('Connection'))
        {
            // 未做处理则做默认握手处理
            $response = $this->defaultHandShake($request, $response);
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