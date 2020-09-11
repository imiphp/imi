<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\FinishEventParam;

/**
 * 监听服务器finish事件接口.
 */
interface IFinishEventListener
{
    /**
     * 事件处理方法.
     *
     * @param FinishEventParam $e
     *
     * @return void
     */
    public function handle(FinishEventParam $e);
}
