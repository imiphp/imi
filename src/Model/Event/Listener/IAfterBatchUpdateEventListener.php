<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterBatchUpdateEventParam;

/**
 * 模型 批量更新后 事件监听接口.
 */
interface IAfterBatchUpdateEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterBatchUpdateEventParam $e
     *
     * @return void
     */
    public function handle(AfterBatchUpdateEventParam $e);
}
