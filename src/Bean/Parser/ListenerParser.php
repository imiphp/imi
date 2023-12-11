<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

use Imi\App;
use Imi\Event\Contract\IEvent;
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
            $one = $annotation->one;
            EventManager::add($eventName, $className, $priority, $one);
            if ($one)
            {
                Event::one($eventName, static fn (IEvent $e) => App::newInstance($className)->handle($e), $priority);
            }
            else
            {
                Event::on($eventName, static fn (IEvent $e) => App::newInstance($className)->handle($e), $priority);
            }
        }
    }
}
