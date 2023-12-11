<?php

declare(strict_types=1);

namespace Imi\Core\Runtime\Event;

use Imi\Core\CoreEvents;
use Imi\Event\CommonEvent;

class BuildRuntimeInfoEvent extends CommonEvent
{
    public function __construct(
        public readonly string $cacheName,
        public array $data,
    ) {
        $this->__eventName = CoreEvents::BUILD_RUNTIME;
    }
}
