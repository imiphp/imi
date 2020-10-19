<?php

namespace Imi\Server\UdpServer;

use Imi\Server\UdpServer\Message\IPacketData;

interface IPacketHandler
{
    /**
     * 返回值为响应内容，为null则无任何响应.
     *
     * @param IPacketData $data
     *
     * @return mixed
     */
    public function handle(IPacketData $data);
}
