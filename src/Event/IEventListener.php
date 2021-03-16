<?php

declare(strict_types=1);

namespace Imi\Event;

/**
 * 监听类接口.
 */
interface IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void;
}
