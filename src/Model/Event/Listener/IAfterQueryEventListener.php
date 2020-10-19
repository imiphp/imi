<?php

namespace Imi\Model\Event\Listener;

use Imi\Model\Event\Param\AfterQueryEventParam;

/**
 * 模型 query查询后 事件监听接口
 * 无论是Find、Select，还是通过Model::query()查询，都会触发该事件.
 */
interface IAfterQueryEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AfterQueryEventParam $e
     *
     * @return void
     */
    public function handle(AfterQueryEventParam $e);
}
