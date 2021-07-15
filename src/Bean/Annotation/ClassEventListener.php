<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 类事件监听.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\ClassEventParser")
 *
 * @property string $className 类名
 * @property string $eventName 事件名
 * @property int    $priority  优先级，越大越先执行
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ClassEventListener extends Base
{
    public function __construct(?array $__data = null, string $className = '', string $eventName = '', int $priority = 0)
    {
        parent::__construct(...\func_get_args());
    }
}
