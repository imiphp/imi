<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Controller;

use Imi\Server\TcpServer\Contract\ITcpServer;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * TCP 控制器.
 */
abstract class TcpController
{
    /**
     * 服务器.
     */
    public ITcpServer $server;

    /**
     * 数据.
     */
    public IReceiveData $data;

    /**
     * 编码消息，把数据编码为发送给客户端的格式.
     *
     * @param mixed $data
     */
    protected function encodeMessage($data): string
    {
        return $this->server->getBean(\Imi\Server\DataParser\DataParser::class)->encode($data);
    }
}
