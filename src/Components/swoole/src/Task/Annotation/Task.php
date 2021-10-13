<?php

declare(strict_types=1);

namespace Imi\Swoole\Task\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;
use Imi\Swoole\Task\TaskParam;

/**
 * 任务注解.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Swoole\Task\Parser\TaskParser")
 *
 * @property string $name       任务名称
 * @property string $paramClass 参数类
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Task extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, string $name = '', string $paramClass = TaskParam::class)
    {
        parent::__construct(...\func_get_args());
    }
}
