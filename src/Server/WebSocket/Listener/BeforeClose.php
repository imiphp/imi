<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\ServerManage;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\CloseEventParam;
use Imi\Server\Event\Listener\ICloseEventListener;

/**
 * Close事件前置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="close",priority=PHP_INT_MAX)
 */
class BeforeClose implements ICloseEventListener
{
    /**
     * 事件处理方法
     * @param CloseEventParam $e
     * @return void
     */
    public function handle(CloseEventParam $e)
    {
        if(!RequestContext::exsits())
        {
            RequestContext::create();
        }
        RequestContext::set('fd', $e->fd);
        RequestContext::set('server', $e->getTarget());
        
    }
}