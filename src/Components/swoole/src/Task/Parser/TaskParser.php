<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\TaskManager;

class TaskParser extends BaseParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        if ($annotation instanceof Task)
        {
            TaskManager::add($annotation->name, $className, $annotation->toArray());
        }
    }
}
