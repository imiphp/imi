<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterParseDataEventParam;

/**
 * 模型 处理 save、insert、update 数据后 事件监听接口.
 */
interface IAfterParseDataEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterParseDataEventParam $e
     *
     * @return void
     */
    public function handle(AfterParseDataEventParam $e);
}
