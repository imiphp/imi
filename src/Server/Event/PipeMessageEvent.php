<?php

declare(strict_types=1);

namespace Imi\Server\Event;

use Imi\Event\CommonEvent;

class PipeMessageEvent extends CommonEvent
{
    public function __construct(string $__eventName, public readonly mixed $data)
    {
        parent::__construct($__eventName);
    }
}
