<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeDeleteEventParam;

/**
 * 模型 删除前 事件监听接口.
 */
interface IBeforeDeleteEventListener
{
    /**
     * 事件处理方法.
     *
     * @param BeforeDeleteEventParam $e
     *
     * @return void
     */
    public function handle(BeforeDeleteEventParam $e);
}
