<?php

declare(strict_types=1);

namespace Imi\Event;

class RegisteredListener
{
    /**
     * @var callable
     */
    public readonly mixed $listener;

    public readonly float $createTime;

    public function __construct(callable $listener,
        /**
         * 优先级
         * 越大越先执行.
         */
        public readonly int $priority = 0,
        /**
         * 事件是否仅允许调用一次.
         */
        public readonly bool $once = false)
    {
        $this->listener = $listener;
        $this->createTime = microtime(true);
    }

    public function __invoke(object $event): void
    {
        ($this->listener)($event);
    }
}
