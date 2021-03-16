<?php

declare(strict_types=1);

namespace Imi\Event;

class EventItem
{
    /**
     * 回调类.
     */
    public ?string $callbackClass = null;

    /**
     * 真实的事件回调.
     *
     * @var callable
     */
    public $callback;

    /**
     * 优先级
     * 越大越先执行.
     */
    public int $priority = 0;

    /**
     * 是否为一次性事件.
     */
    public bool $oneTime = false;

    public function __construct(callable $callback, int $priority = 0, bool $oneTime = false)
    {
        $this->callback = $callback;
        $this->priority = $priority;
        $this->oneTime = $oneTime;
    }
}
