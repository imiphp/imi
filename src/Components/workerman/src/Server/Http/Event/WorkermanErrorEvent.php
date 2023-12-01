<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Event;

use Imi\Event\CommonEvent;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\Contract\IWorkermanServer;
use Workerman\Connection\ConnectionInterface;

class WorkermanErrorEvent extends CommonEvent
{
    public function __construct(
        public readonly IWorkermanServer $server,
        public readonly string|int $clientId,
        public readonly ConnectionInterface $connection,
        public readonly int $code,
        public readonly string $msg
    ) {
        parent::__construct(WorkermanEvents::SERVER_ERROR, $server);
    }
}
