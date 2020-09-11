<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\HandShakeEventParam;

/**
 * 监听服务器HandShake事件接口.
 */
interface IHandShakeEventListener
{
    /**
     * 事件处理方法.
     *
     * @param HandShakeEventParam $e
     *
     * @return void
     */
    public function handle(HandShakeEventParam $e);
}
