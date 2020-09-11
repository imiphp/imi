<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\InitEventParam;

/**
 * 模型 初始化 事件监听接口.
 */
interface IInitEventListener
{
    /**
     * 事件处理方法.
     *
     * @param InitEventParam $e
     *
     * @return void
     */
    public function handle(InitEventParam $e);
}
