<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\TaskEventParam;

/**
 * 监听服务器task事件接口.
 */
interface ITaskEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(TaskEventParam $e): void;
}
