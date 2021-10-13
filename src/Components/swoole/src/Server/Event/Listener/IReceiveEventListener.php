<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\ReceiveEventParam;

/**
 * 监听服务器receive事件接口.
 */
interface IReceiveEventListener
{
    public function handle(ReceiveEventParam $e): void;
}
