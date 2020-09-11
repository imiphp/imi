<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterBatchDeleteEventParam;

/**
 * 模型 批量删除后 事件监听接口.
 */
interface IAfterBatchDeleteEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterBatchDeleteEventParam $e
     *
     * @return void
     */
    public function handle(AfterBatchDeleteEventParam $e);
}
