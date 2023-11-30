<?php

declare(strict_types=1);

namespace Imi\Process\Event;

use Imi\Event\CommonEvent;

class ProcessEndEvent extends CommonEvent
{
    public function __construct(
        public readonly string $name,
        public readonly object $process,
    ) {
        parent::__construct('IMI.PROCESS.END');
    }
}
