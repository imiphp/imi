<?php

namespace Imi\Event;

/**
 * 监听类接口.
 */
interface IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e);
}
