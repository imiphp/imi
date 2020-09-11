<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\CloseEventParam;

/**
 * 监听服务器close事件接口.
 */
interface ICloseEventListener
{
    /**
     * 事件处理方法.
     *
     * @param CloseEventParam $e
     *
     * @return void
     */
    public function handle(CloseEventParam $e);
}
