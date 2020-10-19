<?php

namespace Imi\Bean\Annotation;

/**
 * 类事件监听.
 *
 * @Annotation
 * @Target("CLASS")
 * @Parser("Imi\Bean\Parser\ListenerParser")
 */
class Listener extends Base
{
    /**
     * 只传一个参数时的参数名.
     *
     * @var string
     */
    protected $defaultFieldName = 'eventName';

    /**
     * 事件名.
     *
     * @var string
     */
    public $eventName;

    /**
     * 优先级，越大越先执行.
     *
     * @var int
     */
    public $priority = 0;
}
