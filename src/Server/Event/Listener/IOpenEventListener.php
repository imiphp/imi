<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\OpenEventParam;

/**
 * 监听服务器open事件接口.
 */
interface IOpenEventListener
{
    /**
     * 事件处理方法.
     *
     * @param OpenEventParam $e
     *
     * @return void
     */
    public function handle(OpenEventParam $e);
}
