<?php

declare(strict_types=1);

namespace Imi\Server\Group\Event;

use Imi\Event\CommonEvent;
use Imi\Server\Contract\IServer;

class LeaveGroupEvent extends CommonEvent
{
    public function __construct(
        public readonly IServer $server,
        public readonly string $groupName,
        public readonly string|int $clientId
    ) {
        parent::__construct(ServerGroupEvents::LEAVE_GROUP, $server);
    }
}
