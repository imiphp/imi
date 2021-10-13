<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

use Imi\Bean\PartialManager;

class PartialParser extends BaseParser
{
    /**
     * {@inheritDoc}
     *
     * @param \Imi\Bean\Annotation\Partial $annotation
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        // Partial 仅支持定义为 trait
        if (!trait_exists($className, true))
        {
            return;
        }
        PartialManager::add($className, $annotation->class);
    }
}
