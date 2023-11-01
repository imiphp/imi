<?php

declare(strict_types=1);

namespace Imi\Bean\Annotation;

/**
 * 类事件监听.
 *
 * @Annotation
 *
 * @Target("CLASS")
 *
 * @property string $eventName 事件名
 * @property int    $priority  优先级，越大越先执行
 * @property bool   $one       事件仅触发一次
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
#[\Imi\Bean\Annotation\Parser(className: \Imi\Bean\Parser\ListenerParser::class)]
class Listener extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'eventName';

    public function __construct(?array $__data = null, string $eventName = '', int $priority = 0, bool $one = false)
    {
        parent::__construct(...\func_get_args());
    }
}
