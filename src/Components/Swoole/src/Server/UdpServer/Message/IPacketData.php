<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Message;

interface IPacketData extends \Imi\Server\UdpServer\Message\IPacketData
{
    /**
     * 获取客户端信息.
     */
    public function getClientInfo(): array;
}
