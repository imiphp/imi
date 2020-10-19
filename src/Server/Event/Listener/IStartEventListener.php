<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\StartEventParam;

/**
 * 监听服务器start事件接口.
 */
interface IStartEventListener
{
    /**
     * 事件处理方法.
     *
     * @param StartEventParam $e
     *
     * @return void
     */
    public function handle(StartEventParam $e);
}
