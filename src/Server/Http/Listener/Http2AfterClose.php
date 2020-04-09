<?php
namespace Imi\Server\Http\Listener;

use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;
use Imi\Server\ConnectContext\Traits\TConnectContextRelease;

class Http2AfterClose implements ICloseEventListener
{
    use TConnectContextRelease;

    /**
     * 事件处理方法
     * @param CloseEventParam $e
     * @return void
     */
    public function handle(CloseEventParam $e)
    {
        $this->release($e->fd);
    }

}
