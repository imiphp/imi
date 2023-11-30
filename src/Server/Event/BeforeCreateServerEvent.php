<?php

declare(strict_types=1);

namespace Imi\Server\Event;

use Imi\Event\CommonEvent;

class BeforeCreateServerEvent extends CommonEvent
{
    public function __construct(
        public readonly string $name,
        public readonly array $config,
        public readonly array $args
    ) {
        parent::__construct('IMI.SERVER.CREATE.BEFORE');
    }
}
