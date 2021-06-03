<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\ManagerStartEventParam;

/**
 * 监听服务器ManagerStart事件接口.
 */
interface IManagerStartEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(ManagerStartEventParam $e): void;
}
