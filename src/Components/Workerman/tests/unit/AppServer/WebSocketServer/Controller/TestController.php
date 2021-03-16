<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\WebSocketServer\Controller;

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
     */
    public function login(\stdClass $data): array
    {
        ConnectContext::set('username', $data->username);
        $this->server->joinGroup('g1', $this->frame->getFd());
        $token = md5(uniqid('', true));
        ConnectContext::bind($token);

        return [
            'success'             => true,
            'middlewareData'      => RequestContext::get('middlewareData'),
            'requestUri'          => ConnectContext::get('requestUri'),
            'uri'                 => (string) ConnectContext::get('uri'),
            'token'               => $token,
            'fd'                  => $this->frame->getFd(),
            'getFdByFlag'         => ConnectContext::getFdByFlag($token),
            'getFlagByFd'         => ConnectContext::getFlagByFd($this->frame->getFd()),
            'getFdsByFlags'       => ConnectContext::getFdsByFlags([$token]),
            'getFlagsByFds'       => ConnectContext::getFlagsByFds([$this->frame->getFd()]),
        ];
    }

    /**
     * 重连.
     *
     * @WSAction
     * @WSRoute({"action"="reconnect"})
     */
    public function reconnect(\stdClass $data): array
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
     */
    public function send(\stdClass $data): void
    {
        /** @var \Imi\Workerman\Server\WebSocket\Server $server */
        $server = $this->server;
        $group = $server->getGroup('g1');
        $worker = $server->getWorker();
        $message = ConnectContext::get('username') . ':' . $data->message;
        foreach ($group->getHandler()->getFds('g1') as $fd)
        {
            $worker->connections[$fd]->send($message);
        }
    }

    /**
     * 连接信息.
     *
     * @WSAction
     * @WSRoute({"action"="info"})
     */
    public function info(): array
    {
        return [
            'fd'       => RequestContext::get('fd'),
            'workerId' => Worker::getWorkerId(),
        ];
    }

    /**
     * 多级参数的路由定位.
     *
     * @WSAction
     * @WSRoute({"a.b.c"="test1"})
     */
    public function test1(\stdClass $data): array
    {
        return ['data' => $data];
    }

    /**
     * 多个参数条件的路由定位.
     *
     * @WSAction
     * @WSRoute({"a"="1", "b"=2})
     */
    public function test2(\stdClass $data): array
    {
        return ['data' => $data];
    }

    /**
     * 测试重复路由警告.
     *
     * @WSAction
     * @WSRoute({"duplicated"=1})
     */
    public function duplicated1(): void
    {
    }

    /**
     * 测试重复路由警告.
     *
     * @WSAction
     * @WSRoute({"duplicated"=1})
     */
    public function duplicated2(): void
    {
    }
}
