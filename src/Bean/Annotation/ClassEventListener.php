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
class ClassEventListener extends Base
{
    /**
     * 类名.
     *
     * @var string
     */
    public string $className = '';

    /**
     * 事件名.
     *
     * @var string
     */
    public string $eventName = '';

    /**
     * 优先级，越大越先执行.
     *
     * @var int
     */
    public int $priority = 0;
}
