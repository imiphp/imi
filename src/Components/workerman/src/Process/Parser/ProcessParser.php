<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Workerman\Process\Annotation\Process;
use Imi\Workerman\Process\ProcessManager;

class ProcessParser extends BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        if ($annotation instanceof Process)
        {
            ProcessManager::add($annotation->name, $className, $annotation->toArray());
        }
    }
}
