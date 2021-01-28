<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\UdpServer\Message;

use Workerman\Connection\UdpConnection;

interface IPacketData extends \Imi\Server\UdpServer\Message\IPacketData
{
    /**
     * 获取连接对象
     *
     * @return \Workerman\Connection\UdpConnection
     */
    public function getConnection(): UdpConnection;
}
