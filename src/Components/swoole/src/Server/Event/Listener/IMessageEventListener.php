<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\MessageEventParam;

/**
 * 监听服务器Message事件接口.
 */
interface IMessageEventListener
{
    public function handle(MessageEventParam $e): void;
}
