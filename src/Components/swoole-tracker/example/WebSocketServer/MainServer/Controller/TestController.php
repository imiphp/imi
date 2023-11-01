<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\WebSocketServer\MainServer\Controller;

use Imi\ConnectionContext;
use Imi\Controller\WebSocketController;
use Imi\Server\Server;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSMiddleware;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;

/**
 * 数据收发测试.
 */
#[WSController]
class TestController extends WebSocketController
{
    /**
     * 登录.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    #[WSAction]
    #[WSRoute(condition: ['action' => 'login'])]
    public function login($data)
    {
        ConnectionContext::set('username', $data->username);
        $this->server->joinGroup('g1', $this->frame->getClientId());

        return ['success' => true];
    }

    /**
     * 发送消息.
     *
     * @param mixed $data
     */
    #[WSAction]
    #[WSRoute(condition: ['action' => 'send'])]
    #[WSMiddleware(middlewares: 'Imi\\SwooleTracker\\Example\\WebSocketServer\\MainServer\\Middleware\\Test')]
    public function send($data): void
    {
        $message = ConnectionContext::get('username') . ':' . $data->message;
        Server::sendToGroup('g1', $message);
    }

    /**
     * 多级参数的路由定位.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    #[WSAction]
    #[WSRoute(condition: ['a.b.c' => 'test1'])]
    public function test1($data)
    {
        return ['data' => $data];
    }

    /**
     * 多个参数条件的路由定位.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    #[WSAction]
    #[WSRoute(condition: ['a' => '1', 'b' => 2])]
    public function test2($data)
    {
        return ['data' => $data];
    }
}
