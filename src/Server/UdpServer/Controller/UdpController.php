<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Controller;

use Imi\Server\Annotation\ServerInject;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * UDP 控制器.
 */
abstract class UdpController
{
    /**
     * 包数据.
     *
     * @ServerInject("UdpPacketDataProxy")
     */
    public IPacketData $data;
}
