<?php
namespace Imi\Test\WebSocketServer\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Controller\WebSocketController;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSController;
use Imi\Server\Route\Annotation\WebSocket\WSMiddleware;
use Imi\Event\Event;

/**
 * 数据收发测试
 * @WSController
 */
class TestController extends WebSocketController
{
    /**
     * 登录
     * 
     * @WSAction
     * @WSRoute({"action"="login"})
     * @return void
     */
    public function login($data)
    {
        ConnectContext::set('username', $data->username);
        $this->server->joinGroup('g1', $this->frame->getFd());
        return ['success'=>true];
    }

    /**
     * 发送消息
     *
     * @WSAction
     * @WSRoute({"action"="send"})
     * @WSMiddleware(Imi\Test\WebSocketServer\MainServer\Middleware\Test::class)
     * @param 
     * @return void
     */
    public function send($data)
    {
        $message = ConnectContext::get('username') . ':' . $data->message;
        $this->server->groupCall('g1', 'push', $message);
    }

    /**
     * 多级参数的路由定位
     *
     * @WSAction
     * @WSRoute({"a.b.c"="test1"})
     * @param 
     * @return void
     */
    public function test1($data)
    {
        return ['data'=>$data];
    }

    /**
     * 多个参数条件的路由定位
     *
     * @WSAction
     * @WSRoute({"a"="1", "b"=2})
     * @param 
     * @return void
     */
    public function test2($data)
    {
        return ['data'=>$data];
    }
}