<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

use Imi\Event\ClassEventManager;

class ClassEventParser extends BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        if ($annotation instanceof \Imi\Bean\Annotation\ClassEventListener)
        {
            ClassEventManager::add($annotation->className, $annotation->eventName, $className, $annotation->priority);
        }
    }
}
