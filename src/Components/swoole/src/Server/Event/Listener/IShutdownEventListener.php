<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\ShutdownEventParam;

/**
 * 监听服务器shutdown事件接口.
 */
interface IShutdownEventListener
{
    public function handle(ShutdownEventParam $e): void;
}
