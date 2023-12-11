<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket\Event;

use Imi\Event\CommonEvent;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Workerman\Event\WorkermanEvents;
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
        parent::__construct(WorkermanEvents::SERVER_WEBSOCKET_MESSAGE, $server);
    }
}
