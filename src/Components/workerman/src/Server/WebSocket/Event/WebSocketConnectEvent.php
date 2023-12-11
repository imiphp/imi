<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket\Event;

use Imi\Event\CommonEvent;
use Imi\Server\Contract\IServer;
use Imi\Server\Http\Message\Request;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Http\Message\WorkermanResponse;
use Workerman\Connection\ConnectionInterface;

class WebSocketConnectEvent extends CommonEvent
{
    public function __construct(
        public readonly IServer $server,
        public readonly string|int $clientId,
        public readonly Request $request,
        public readonly ?WorkermanResponse $response = null,
        public readonly ?ConnectionInterface $connection = null,
    ) {
        parent::__construct(WorkermanEvents::SERVER_WEBSOCKET_CONNECT, $server);
    }
}
