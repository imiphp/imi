<?php

declare(strict_types=1);

namespace Imi\Event;

use Imi\Event\Contract\IEvent;

/**
 * 监听类接口.
 */
interface IEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(IEvent $e): void;
}
