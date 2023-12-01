<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Event;

use Imi\Event\CommonEvent;
use Imi\Server\Contract\IServer;
use Imi\Workerman\Event\WorkermanEvents;
use Workerman\Connection\ConnectionInterface;

class ConnectEvent extends CommonEvent
{
    public function __construct(
        public readonly IServer $server,
        public readonly string|int $clientId,
        public readonly ?ConnectionInterface $connection = null
    ) {
        parent::__construct(WorkermanEvents::SERVER_CONNECT, $server);
    }
}
