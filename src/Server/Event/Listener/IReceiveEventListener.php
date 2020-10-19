<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\ReceiveEventParam;

/**
 * 监听服务器receive事件接口.
 */
interface IReceiveEventListener
{
    /**
     * 事件处理方法.
     *
     * @param ReceiveEventParam $e
     *
     * @return void
     */
    public function handle(ReceiveEventParam $e);
}
