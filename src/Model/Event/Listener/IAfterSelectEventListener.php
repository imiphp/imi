<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterSelectEventParam;

/**
 * 模型 查询后 事件监听接口.
 */
interface IAfterSelectEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterSelectEventParam $e
     *
     * @return void
     */
    public function handle(AfterSelectEventParam $e);
}
