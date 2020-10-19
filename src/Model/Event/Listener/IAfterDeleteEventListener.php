<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterDeleteEventParam;

/**
 * 模型 删除后 事件监听接口.
 */
interface IAfterDeleteEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterDeleteEventParam $e
     *
     * @return void
     */
    public function handle(AfterDeleteEventParam $e);
}
