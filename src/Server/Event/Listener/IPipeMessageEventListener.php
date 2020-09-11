<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\PipeMessageEventParam;

/**
 * 监听服务器PipeMessage事件接口.
 */
interface IPipeMessageEventListener
{
    /**
     * 事件处理方法.
     *
     * @param PipeMessageEventParam $e
     *
     * @return void
     */
    public function handle(PipeMessageEventParam $e);
}
