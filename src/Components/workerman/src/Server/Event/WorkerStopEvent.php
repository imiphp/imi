<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Event;

use Imi\Event\CommonEvent;
use Imi\Server\Contract\IServer;
use Workerman\Worker;

class WorkerStopEvent extends CommonEvent
{
    public function __construct(
        public readonly IServer $server,
        public readonly Worker $worker,
    ) {
        parent::__construct('IMI.WORKERMAN.SERVER.WORKER_STOP', $server);
    }
}
