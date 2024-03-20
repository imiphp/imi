<?php

declare(strict_types=1);

namespace Imi\Rpc\Route\Event;

use Imi\Event\CommonEvent;

class RpcAnnotationParserEvent extends CommonEvent
{
    public function __construct(
        string $__eventName,
        ?object $__target,
        public readonly \Imi\Bean\Annotation\Base $annotation,
        public readonly string $className,
        public readonly string $target,
        public readonly string $targetName
    ) {
        parent::__construct($__eventName, $__target);
    }
}
