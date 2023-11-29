<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\Event\Contract\IEvent;

class CommonEvent implements IEvent
{
    /**
     * 阻止事件继续传播.
     */
    protected bool $__stopPropagation = false;

    public function __construct(
        /**
         * 事件名称.
         */
        protected string $__eventName,
        protected ?object $__target = null
    ) {
    }

    public function getEventName(): string
    {
        return $this->__eventName;
    }

    public function getTarget(): ?object
    {
        return $this->__target;
    }

    public function stopPropagation(bool $isStop = true): void
    {
        $this->__stopPropagation = $isStop;
    }

    public function isPropagationStopped(): bool
    {
        return $this->__stopPropagation;
    }
}
