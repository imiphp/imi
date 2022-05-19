<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 类事件监听.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\ListenerParser")
 *
 * @property string $eventName 事件名
 * @property int    $priority  优先级，越大越先执行
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Listener extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'eventName';

    public function __construct(?array $__data = null, string $eventName = '', int $priority = 0)
    {
        parent::__construct(...\func_get_args());
    }
}
