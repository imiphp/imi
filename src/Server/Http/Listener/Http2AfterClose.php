<?php
namespace Imi\Server\Http\Listener;

use Imi\ConnectContext;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;

class Http2AfterClose implements ICloseEventListener
{
    /**
     * 事件处理方法
     * @param CloseEventParam $e
     * @return void
     */
    public function handle(CloseEventParam $e)
    {
        ConnectContext::destroy();
    }

}
