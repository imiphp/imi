<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Controller\Http;

use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\View\Annotation\View;
use Imi\Util\Http\Consts\StatusCode;

/**
 * 手动握手测试，不会触发框架内置的握手处理.
 *
 * @Controller
 *
 * @View(renderType="html")
 */
class HandShake extends HttpController
{
    /**
     * @Action
     *
     * @Route("/test")
     */
    public function index(): void
    {
        // 手动握手处理

        $secWebSocketKey = $this->request->getHeaderLine('sec-websocket-key');
        if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $secWebSocketKey) || 16 !== \strlen(base64_decode($secWebSocketKey)))
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

        if ($this->request->hasHeader('Sec-WebSocket-Protocol'))
        {
            $headers['Sec-WebSocket-Protocol'] = $this->request->getHeaderLine('Sec-WebSocket-Protocol');
        }

        foreach ($headers as $key => $val)
        {
            $this->response = $this->response->withHeader($key, $val);
        }

        $this->response = $this->response->withStatus(StatusCode::SWITCHING_PROTOCOLS);

        var_dump('testHandShake');
    }
}
