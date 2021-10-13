<?php

declare(strict_types=1);

namespace Imi\Enum\Annotation\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\EnumManager;

class EnumParser extends BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        if ($annotation instanceof EnumItem)
        {
            EnumManager::add($className, $targetName, $annotation->toArray());
        }
    }
}
