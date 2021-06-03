<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Udp\Message;

use Workerman\Connection\UdpConnection;

interface IPacketData extends \Imi\Server\UdpServer\Message\IPacketData
{
    /**
     * 获取连接对象
     */
    public function getConnection(): UdpConnection;
}
