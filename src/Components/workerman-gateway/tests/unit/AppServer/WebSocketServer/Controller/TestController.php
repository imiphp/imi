<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Controller;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\Server;
use Imi\Server\WebSocket\Controller\WebSocketController;
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
        // @phpstan-ignore-next-line
        $this->server->joinGroup('g1', $this->frame->getClientId());
        ConnectContext::bind($data->username);

        return [
            'success'                               => true,
            'middlewareData'                        => RequestContext::get('middlewareData'),
            'requestUri'                            => ConnectContext::get('requestUri'),
            'uri'                                   => (string) ConnectContext::get('uri'),
            'token'                                 => $data->username,
            'clientId'                              => $this->frame->getClientId(),
            'getClientIdByFlag'                     => ConnectContext::getClientIdByFlag($data->username),
            'getFlagByClientId'                     => ConnectContext::getFlagByClientId($this->frame->getClientId()),
            'getClientIdsByFlags'                   => ConnectContext::getClientIdsByFlags([$data->username]),
            'getFlagsByClientIds'                   => ConnectContext::getFlagsByClientIds([$this->frame->getClientId()]),
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
        $message = ConnectContext::get('username') . ':' . $data->message;
        Server::sendRawToGroup('g1', $message);
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
            'clientId'       => ConnectContext::getClientId(),
            'workerId'       => Worker::getWorkerId(),
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
