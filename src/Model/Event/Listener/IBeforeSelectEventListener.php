<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeSelectEventParam;

/**
 * 模型 查询前 事件监听接口.
 */
interface IBeforeSelectEventListener
{
    /**
     * 事件处理方法.
     *
     * @param BeforeSelectEventParam $e
     *
     * @return void
     */
    public function handle(BeforeSelectEventParam $e);
}
