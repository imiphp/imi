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
        $groups = ConnectContext::get('__groups', []);

        // 当前连接离开所有组
        $e->getTarget()->getBean('FdMap')->leaveAll($e->fd);

        ConnectContext::set('__groups', $groups);

        ConnectContext::destroy($e->fd);
    }

}
