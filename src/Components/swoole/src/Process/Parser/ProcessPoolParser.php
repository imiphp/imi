<?php

declare(strict_types=1);

namespace Imi\Swoole\Process\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Swoole\Process\Annotation\ProcessPool;
use Imi\Swoole\Process\ProcessPoolManager;

class ProcessPoolParser extends BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        if ($annotation instanceof ProcessPool)
        {
            ProcessPoolManager::add($annotation->name, $className, $annotation->toArray());
        }
    }
}
