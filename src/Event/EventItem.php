<?php

namespace Imi\Event;

class EventItem
{
    /**
     * 回调类.
     *
     * @var string
     */
    public $callbackClass;

    /**
     * 真实的事件回调.
     *
     * @var callable
     */
    public $callback;

    /**
     * 优先级
     * 越大越先执行.
     *
     * @var int
     */
    public $priority;

    /**
     * 是否为一次性事件.
     *
     * @var bool
     */
    public $oneTime;

    public function __construct($callback, int $priority = 0, bool $oneTime = false)
    {
        $this->callback = $callback;
        $this->priority = $priority;
        $this->oneTime = $oneTime;
    }
}
