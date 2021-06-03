<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Event\Listener;

use Imi\Swoole\Server\Event\Param\RequestEventParam;

/**
 * 监听服务器request事件接口.
 */
interface IRequestEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(RequestEventParam $e): void;
}
