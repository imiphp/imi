<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\BeforeParseDataEventParam;

/**
 * 模型 处理 save、insert、update 数据前 事件监听接口.
 */
interface IBeforeParseDataEventListener
{
    /**
     * 事件处理方法.
     *
     * @param BeforeParseDataEventParam $e
     *
     * @return void
     */
    public function handle(BeforeParseDataEventParam $e);
}
