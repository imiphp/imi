<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\Swoole\Server\ConnectContext\Traits\TConnectContextRelease;
use Imi\Swoole\Server\Event\Listener\ICloseEventListener;
use Imi\Swoole\Server\Event\Param\CloseEventParam;

/**
 * Close事件后置处理.
 *
 * @ClassEventListener(className="Imi\Swoole\Server\TcpServer\Server",eventName="close",priority=Imi\Util\ImiPriority::IMI_MIN)
 */
class AfterClose implements ICloseEventListener
{
    use TConnectContextRelease;

    /**
     * 事件处理方法.
     *
     * @param CloseEventParam $e
     *
     * @return void
     */
    public function handle(CloseEventParam $e)
    {
        $this->release($e->fd);
    }
}
