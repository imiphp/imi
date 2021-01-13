<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\CloseEventParam;

/**
 * 监听服务器close事件接口.
 */
interface ICloseEventListener
{
    /**
     * 事件处理方法.
     *
     * @param CloseEventParam $e
     *
     * @return void
     */
    public function handle(CloseEventParam $e);
}
