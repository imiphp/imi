<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServer\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Controller\WebSocketController;
use Imi\RequestContext;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;
use Imi\Worker;

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
     * @return void
     */
    public function login($data)
    {
        ConnectContext::set('username', $data->username);
        $this->server->joinGroup('g1', $this->frame->getFd());
        ConnectContext::bind($data->username);

        return [
            'success'             => true,
            'middlewareData'      => RequestContext::get('middlewareData'),
            'requestUri'          => ConnectContext::get('requestUri'),
            'uri'                 => (string) ConnectContext::get('uri'),
            'fd'                  => $this->frame->getFd(),
            'getFdByFlag'         => ConnectContext::getFdByFlag($data->username),
            'getFlagByFd'         => ConnectContext::getFlagByFd($this->frame->getFd()),
            'getFdsByFlags'       => ConnectContext::getFdsByFlags([$data->username]),
            'getFlagsByFds'       => ConnectContext::getFlagsByFds([$this->frame->getFd()]),
        ];
    }

    /**
     * 重连.
     *
     * @WSAction
     * @WSRoute({"action"="reconnect"})
     *
     * @return void
     */
    public function reconnect($data)
    {
        ConnectContext::restore($data->token);

        return [
            'success'   => true,
            'username'  => ConnectContext::get('username'),
        ];
    }

    /**
     * 发送消息.
     *
     * @WSAction
     * @WSRoute({"action"="send"})
     *
     * @param
     *
     * @return void
     */
    public function send($data)
    {
        $message = ConnectContext::get('username') . ':' . $data->message;
        $this->server->groupCall('g1', 'push', $message);
    }

    /**
     * 连接信息.
     *
     * @WSAction
     * @WSRoute({"action"="info"})
     *
     * @param
     *
     * @return void
     */
    public function info()
    {
        return [
            'fd'        => RequestContext::get('fd'),
            'workerId'  => Worker::getWorkerId(),
        ];
    }

    /**
     * 多级参数的路由定位.
     *
     * @WSAction
     * @WSRoute({"a.b.c"="test1"})
     *
     * @param
     *
     * @return void
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
     * @param
     *
     * @return void
     */
    public function test2($data)
    {
        return ['data' => $data];
    }

    /**
     * 测试重复路由警告.
     *
     * @WSAction
     * @WSRoute({"duplicated"=1})
     *
     * @param
     *
     * @return void
     */
    public function duplicated1($data)
    {
    }

    /**
     * 测试重复路由警告.
     *
     * @WSAction
     * @WSRoute({"duplicated"=1})
     *
     * @param
     *
     * @return void
     */
    public function duplicated2($data)
    {
    }
}
