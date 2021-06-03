<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\PipeMessageEventParam;

/**
 * 监听服务器PipeMessage事件接口.
 */
interface IPipeMessageEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(PipeMessageEventParam $e): void;
}
