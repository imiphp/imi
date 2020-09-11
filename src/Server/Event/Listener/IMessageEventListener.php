<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\MessageEventParam;

/**
 * 监听服务器Message事件接口.
 */
interface IMessageEventListener
{
    /**
     * 事件处理方法.
     *
     * @param MessageEventParam $e
     *
     * @return void
     */
    public function handle(MessageEventParam $e);
}
