<?php

declare(strict_types=1);

namespace Imi\Pool\Event;

use Imi\Event\CommonEvent;

class CheckPoolResourceEvent extends CommonEvent
{
    public function __construct(
        public bool $result
    ) {
        parent::__construct('IMI.CHECK_POOL_RESOURCE');
    }
}
