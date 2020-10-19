<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterInsertEventParam;

/**
 * 模型 插入后 事件监听接口.
 */
interface IAfterInsertEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterInsertEventParam $e
     *
     * @return void
     */
    public function handle(AfterInsertEventParam $e);
}
