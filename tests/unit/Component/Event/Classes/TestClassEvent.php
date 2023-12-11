<?php

declare(strict_types=1);

namespace Imi\Test\Component\Event\Classes;

use Imi\Event\CommonEvent;

class TestClassEvent extends CommonEvent
{
    public function __construct(
        string $__eventName,
        ?object $__target,
        public readonly string $name,
        public mixed $return = null
    ) {
        parent::__construct($__eventName, $__target);
    }
}
