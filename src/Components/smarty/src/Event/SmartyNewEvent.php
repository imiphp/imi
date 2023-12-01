<?php

declare(strict_types=1);

namespace Imi\Smarty\Event;

use Imi\Event\CommonEvent;

class SmartyNewEvent extends CommonEvent
{
    public function __construct(
        public readonly \Smarty $smarty,
        public readonly string $serverName,
    ) {
        parent::__construct(SmartyEvents::NEW_SMARTY);
    }
}
