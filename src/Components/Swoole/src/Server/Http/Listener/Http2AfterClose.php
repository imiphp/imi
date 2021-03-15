<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Listener;

use Imi\Server\ConnectContext\Traits\TConnectContextRelease;
use Imi\Swoole\Server\Event\Listener\ICloseEventListener;
use Imi\Swoole\Server\Event\Param\CloseEventParam;

class Http2AfterClose implements ICloseEventListener
{
    use TConnectContextRelease;

    /**
     * 事件处理方法.
     *
     * @param CloseEventParam $e
     *
     * @return void
     */
    public function handle(CloseEventParam $e): void
    {
        $this->release($e->fd);
    }
}
