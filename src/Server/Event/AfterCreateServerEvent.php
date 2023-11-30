<?php

declare(strict_types=1);

namespace Imi\Server\Event;

use Imi\Event\CommonEvent;
use Imi\Server\Contract\IServer;

class AfterCreateServerEvent extends CommonEvent
{
    public function __construct(
        public readonly string $name,
        public readonly array $config,
        public readonly array $args,
        public readonly IServer $server
    ) {
        parent::__construct('IMI.SERVER.CREATE.AFTER');
    }
}
