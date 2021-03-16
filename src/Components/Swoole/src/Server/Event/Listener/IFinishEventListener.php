<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\FinishEventParam;

/**
 * 监听服务器finish事件接口.
 */
interface IFinishEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(FinishEventParam $e): void;
}
