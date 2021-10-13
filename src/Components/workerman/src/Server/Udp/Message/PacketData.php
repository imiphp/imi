<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Udp\Message;

use Workerman\Connection\UdpConnection;

class PacketData extends \Imi\Server\UdpServer\Message\PacketData implements IPacketData
{
    /**
     * 连接对象.
     */
    protected UdpConnection $connection;

    /**
     * {@inheritDoc}
     */
    public function __construct(UdpConnection $connection, string $data)
    {
        parent::__construct($connection->getRemoteIp(), $connection->getRemotePort(), $data);
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnection(): UdpConnection
    {
        return $this->connection;
    }
}
