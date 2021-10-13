<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

use Imi\Event\Event;
use Imi\Event\EventManager;

class ListenerParser extends BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        if ($annotation instanceof \Imi\Bean\Annotation\Listener)
        {
            $eventName = $annotation->eventName;
            $priority = $annotation->priority;
            EventManager::add($eventName, $className, $priority);
            Event::on($eventName, $className, $priority);
        }
    }
}
