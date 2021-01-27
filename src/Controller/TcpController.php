<?php

namespace Imi\Controller;

/**
 * TCP 控制器.
 */
abstract class TcpController
{
    /**
     * 服务器.
     *
     * @var \Imi\Server\TcpServer\Server
     */
    public $server;

    /**
     * 数据.
     *
     * @var \Imi\Server\TcpServer\Message\IReceiveData
     */
    public $data;

    /**
     * 编码消息，把数据编码为发送给客户端的格式.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected function encodeMessage($data)
    {
        return $this->server->getBean(\Imi\Server\DataParser\DataParser::class)->encode($data);
    }
}
