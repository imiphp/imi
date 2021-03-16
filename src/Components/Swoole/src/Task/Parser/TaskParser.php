<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Swoole\Task\Annotation\Task;
use Imi\Swoole\Task\TaskManager;

class TaskParser extends BaseParser
{
    /**
     * 处理方法.
     *
     * @param \Imi\Bean\Annotation\Base $annotation 注解类
     * @param string                    $className  类名
     * @param string                    $target     注解目标类型（类/属性/方法）
     * @param string                    $targetName 注解目标名称
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
        if ($annotation instanceof Task)
        {
            TaskManager::add($annotation->name, $className, $annotation->toArray());
        }
    }
}
