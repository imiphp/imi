<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeFindEventParam;

/**
 * 模型 查找前 事件监听接口.
 */
interface IBeforeFindEventListener
{
    /**
     * 事件处理方法.
     *
     * @param BeforeFindEventParam $e
     *
     * @return void
     */
    public function handle(BeforeFindEventParam $e);
}
