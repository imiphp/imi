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

    public function __construct(callable $callback, /**
     * 优先级
     * 越大越先执行.
     */
        public int $priority = 0, /**
     * 是否为一次性事件.
     */
        public bool $oneTime = false)
    {
        $this->callback = $callback;
    }
}
