<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\TCPServer\MainServer\Controller;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\TcpServer\Route\Annotation\TcpAction;
use Imi\Server\TcpServer\Route\Annotation\TcpController;
use Imi\Server\TcpServer\Route\Annotation\TcpRoute;

/**
 * 数据收发测试.
 *
 * @TcpController
 */
class TestController extends \Imi\Controller\TcpController
{
    /**
     * 登录.
     *
     * @TcpAction
     * @TcpRoute({"action"="login"})
     */
    public function login(\stdClass $data): array
    {
        ConnectContext::set('username', $data->username);
        $this->server->joinGroup('g1', $this->data->getFd());
        ConnectContext::bind($data->username);

        return ['action' => 'login', 'success' => true, 'middlewareData' => RequestContext::get('middlewareData')];
    }

    /**
     * 发送消息.
     *
     * @TcpAction
     * @TcpRoute({"action"="send"})
     */
    public function send(\stdClass $data): void
    {
        $message = [
            'action'     => 'send',
            'message'    => ConnectContext::get('username') . ':' . $data->message,
        ];
        $this->server->groupCall('g1', 'send', $this->server->getBean(\Imi\Server\DataParser\DataParser::class)->encode($message));
    }

    /**
     * 测试重复路由警告.
     *
     * @TcpAction
     * @TcpRoute({"duplicated"="1"})
     */
    public function duplicated1(): void
    {
    }

    /**
     * 测试重复路由警告.
     *
     * @TcpAction
     * @TcpRoute({"duplicated"="1"})
     */
    public function duplicated2(): void
    {
    }
}
