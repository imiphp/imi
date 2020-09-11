<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeBatchDeleteEventParam;

/**
 * 模型 批量删除前 事件监听接口.
 */
interface IBeforeBatchDeleteEventListener
{
    /**
     * 事件处理方法.
     *
     * @param BeforeBatchDeleteEventParam $e
     *
     * @return void
     */
    public function handle(BeforeBatchDeleteEventParam $e);
}
