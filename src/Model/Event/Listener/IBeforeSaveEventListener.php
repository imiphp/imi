<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeSaveEventParam;

/**
 * 模型 保存前 事件监听接口.
 */
interface IBeforeSaveEventListener
{
    /**
     * 事件处理方法.
     *
     * @param BeforeSaveEventParam $e
     *
     * @return void
     */
    public function handle(BeforeSaveEventParam $e);
}
