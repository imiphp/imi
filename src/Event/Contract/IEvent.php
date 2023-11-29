<?php

declare(strict_types=1);

namespace Imi\Event\Contract;

interface IEvent
{
    /**
     * 获取事件名称.
     */
    public function getEventName(): string;

    /**
     * 阻止事件继续传播.
     *
     * @param bool $stoped 是否阻止事件继续传播
     */
    public function stopPropagation(bool $stoped = true): void;

    /**
     * 是否阻止事件继续传播.
     */
    public function isPropagationStopped(): bool;
}
