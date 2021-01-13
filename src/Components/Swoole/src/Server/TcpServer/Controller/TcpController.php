<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Controller;

use Imi\Swoole\Server\TcpServer\Message\IReceiveData;
use Imi\Swoole\Server\TcpServer\Server;

/**
 * TCP 控制器.
 */
abstract class TcpController
{
    /**
     * 请求
     *
     * @var \Imi\Swoole\Server\TcpServer\Server
     */
    public Server $server;

    /**
     * 桢.
     *
     * @var \Imi\Swoole\Server\TcpServer\Message\IReceiveData
     */
    public IReceiveData $data;

    /**
     * 编码消息，把数据编码为发送给客户端的格式.
     *
     * @param mixed $data
     *
     * @return string
     */
    protected function encodeMessage($data): string
    {
        return $this->server->getBean(\Imi\Swoole\Server\DataParser\DataParser::class)->encode($data);
    }
}
