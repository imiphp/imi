<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Event;

use Imi\Event\CommonEvent;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Workerman\Connection\ConnectionInterface;

class WorkermanConnectionCloseEvent extends CommonEvent
{
    public function __construct(
        public readonly IWorkermanServer $server,
        public readonly string|int $clientId,
        public readonly ?ConnectionInterface $connection = null,
    ) {
        parent::__construct(WorkermanEvents::SERVER_CLOSE, $server);
    }
}
