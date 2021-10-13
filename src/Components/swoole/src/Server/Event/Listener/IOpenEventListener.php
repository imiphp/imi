<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\OpenEventParam;

/**
 * 监听服务器open事件接口.
 */
interface IOpenEventListener
{
    public function handle(OpenEventParam $e): void;
}
