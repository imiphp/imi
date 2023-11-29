<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Event;

use Imi\Event\CommonEvent;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Workerman\Connection\TcpConnection;

class WorkermanWebSocketMessageEvent extends CommonEvent
{
    public function __construct(
        public readonly IWorkermanServer $server,
        public readonly string|int $clientId,
        public readonly mixed $data,
        public readonly IFrame $frame,
        public readonly ?TcpConnection $connection = null,
    ) {
        parent::__construct('IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE', $server);
    }
}
