<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeBatchUpdateEventParam;

/**
 * 模型 批量更新前 事件监听接口.
 */
interface IBeforeBatchUpdateEventListener
{
    /**
     * 事件处理方法.
     *
     * @param BeforeBatchUpdateEventParam $e
     *
     * @return void
     */
    public function handle(BeforeBatchUpdateEventParam $e);
}
