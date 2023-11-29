<?php

declare(strict_types=1);

namespace Imi\Core\Runtime\Event;

use Imi\Event\CommonEvent;

class LoadRuntimeInfoEvent extends CommonEvent
{
    public bool $success = false;

    public function __construct(
        public readonly string $cacheName,
        public array $data,
        public readonly bool $onlyImi
    ) {
        $this->__eventName = 'IMI.LOAD_RUNTIME_INFO';
    }
}
