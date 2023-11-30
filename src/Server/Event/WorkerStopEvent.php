<?php

declare(strict_types=1);

namespace Imi\Server\Event;

use Imi\Event\CommonEvent;
use Imi\Server\Contract\IServer;

class WorkerStopEvent extends CommonEvent
{
    public function __construct(
        public readonly IServer $server,
        public readonly int $workerId
    ) {
        parent::__construct('IMI.SERVER.WORKER_STOP', $server);
    }
}
