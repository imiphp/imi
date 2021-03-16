<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 类事件监听.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\ClassEventParser")
 */
#[\Attribute]
class ClassEventListener extends Base
{
    /**
     * 类名.
     */
    public string $className = '';

    /**
     * 事件名.
     */
    public string $eventName = '';

    /**
     * 优先级，越大越先执行.
     */
    public int $priority = 0;

    public function __construct(?array $__data = null, string $className = '', string $eventName = '', int $priority = 0)
    {
        parent::__construct(...\func_get_args());
    }
}
