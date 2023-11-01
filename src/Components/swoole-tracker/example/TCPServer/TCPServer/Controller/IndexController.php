<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\TCPServer\TCPServer\Controller;

use Imi\Server\TcpServer\Route\Annotation\TcpAction;
use Imi\Server\TcpServer\Route\Annotation\TcpController;
use Imi\Server\TcpServer\Route\Annotation\TcpRoute;

/**
 * 数据收发测试.
 */
#[TcpController]
class IndexController extends \Imi\Controller\TcpController
{
    /**
     * 发送消息.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    #[TcpAction]
    #[TcpRoute(condition: ['action' => 'send'])]
    public function send($data)
    {
        $address = $this->data->getClientAddress();
        $message = '[' . $address->getAddress() . ':' . $address->getPort() . ']: ' . $data->message;
        var_dump($message);

        return [
            'success'   => true,
            'data'      => $message,
        ];
    }
}
