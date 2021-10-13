<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Event\EventParam;

/**
 * 监听项目初始化事件接口.
 */
interface IAppInitEventListener
{
    public function handle(EventParam $e): void;
}
