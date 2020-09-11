<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\RequestEventParam;

/**
 * 监听服务器request事件接口.
 */
interface IRequestEventListener
{
    /**
     * 事件处理方法.
     *
     * @param RequestEventParam $e
     *
     * @return void
     */
    public function handle(RequestEventParam $e);
}
