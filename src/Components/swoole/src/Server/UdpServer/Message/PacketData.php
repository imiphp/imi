<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Message;

class PacketData extends \Imi\Server\UdpServer\Message\PacketData implements IPacketData
{
    /**
     * 客户端信息.
     */
    protected array $clientInfo = [];

    public function __construct(string $remoteIp, int $remotePort, string $data, array $clientInfo)
    {
        parent::__construct($remoteIp, $remotePort, $data);
        $this->clientInfo = $clientInfo;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientInfo(): array
    {
        return $this->clientInfo;
    }
}
