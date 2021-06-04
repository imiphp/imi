<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\WebSocketServer\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Controller\WebSocketController;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;
use Imi\Server\Route\Annotation\WebSocket\WSMiddleware;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Server;

/**
 * 数据收发测试.
 *
 * @WSController
 */
class TestController extends WebSocketController
{
    /**
     * 登录.
     *
     * @WSAction
     * @WSRoute({"action"="login"})
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function login($data)
    {
        ConnectContext::set('username', $data->username);
        // @phpstan-ignore-next-line
        $this->server->joinGroup('g1', $this->frame->getClientId());

        return ['success' => true];
    }

    /**
     * 发送消息.
     *
     * @WSAction
     * @WSRoute({"action"="send"})
     * @WSMiddleware(Imi\SwooleTracker\Example\WebSocketServer\MainServer\Middleware\Test::class)
     *
     * @param mixed $data
     *
     * @return void
     */
    public function send($data)
    {
        $message = ConnectContext::get('username') . ':' . $data->message;
        Server::sendToGroup('g1', $message);
    }

    /**
     * 多级参数的路由定位.
     *
     * @WSAction
     * @WSRoute({"a.b.c"="test1"})
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function test1($data)
    {
        return ['data' => $data];
    }

    /**
     * 多个参数条件的路由定位.
     *
     * @WSAction
     * @WSRoute({"a"="1", "b"=2})
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function test2($data)
    {
        return ['data' => $data];
    }
}
