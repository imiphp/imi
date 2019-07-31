<?php
namespace Imi\Test\TCPServer\MainServer\Controller;

use Imi\ConnectContext;
use Imi\Server\Route\Annotation\Tcp\TcpRoute;
use Imi\Server\Route\Annotation\Tcp\TcpAction;
use Imi\Server\Route\Annotation\Tcp\TcpController;

/**
 * 数据收发测试
 * @TcpController
 */
class TestController extends \Imi\Controller\TcpController
{
    /**
     * 登录
     * 
     * @TcpAction
     * @TcpRoute({"action"="login"})
     * @return void
     */
    public function login($data)
    {
        $server = $this->server->getSwooleServer();
        $server->send($this->encodeMessage($data));
        ConnectContext::set('username', $data->username);
        $this->server->joinGroup('g1', $this->data->getFd());
        return ['action'=>'login', 'success'=>true];
    }

    /**
     * 发送消息
     *
     * @TcpAction
     * @TcpRoute({"action"="send"})
     * @param 
     * @return void
     */
    public function send($data)
    {
        $message = [
            'action'    =>    'send',
            'message'    =>    ConnectContext::get('username') . ':' . $data->message,
        ];
        $this->server->groupCall('g1', 'send', $this->server->getBean(\Imi\Server\DataParser\DataParser::class)->encode($message));
    }

}