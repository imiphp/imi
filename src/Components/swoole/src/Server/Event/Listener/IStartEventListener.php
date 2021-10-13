<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\StartEventParam;

/**
 * 监听服务器start事件接口.
 */
interface IStartEventListener
{
    public function handle(StartEventParam $e): void;
}
