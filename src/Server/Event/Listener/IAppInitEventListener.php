<?php

namespace Imi\Server\Event\Listener;

use Imi\Server\Event\Param\AppInitEventParam;

/**
 * 监听项目初始化事件接口
 * 项目初始化事件在 ID 为 0 的 WorkerStart 中触发.
 */
interface IAppInitEventListener
{
    /**
     * 事件处理方法.
     *
     * @param AppInitEventParam $e
     *
     * @return void
     */
    public function handle(AppInitEventParam $e);
}
