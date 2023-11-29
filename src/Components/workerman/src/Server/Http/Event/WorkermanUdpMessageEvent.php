<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Event;

use Imi\Event\CommonEvent;
use Imi\Server\UdpServer\Message\IPacketData;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Workerman\Connection\UdpConnection;

class WorkermanUdpMessageEvent extends CommonEvent
{
    public function __construct(
        public readonly IWorkermanServer $server,
        public readonly mixed $data,
        public readonly IPacketData $packetData,
        public readonly UdpConnection $connection,
    ) {
        parent::__construct('IMI.WORKERMAN.SERVER.UDP.MESSAGE', $server);
    }
}
