<?php

declare(strict_types=1);

namespace Imi\Bean\Parser;

/**
 * 不做操作的空Parser.
 */
class NullParser extends BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
    }
}
