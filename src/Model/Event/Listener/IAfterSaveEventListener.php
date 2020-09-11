<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterSaveEventParam;

/**
 * 模型 保存后 事件监听接口.
 */
interface IAfterSaveEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterSaveEventParam $e
     *
     * @return void
     */
    public function handle(AfterSaveEventParam $e);
}
