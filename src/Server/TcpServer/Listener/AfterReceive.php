<?php
namespace Imi\Server\TcpServer\Listener;

use Imi\App;
use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\ReceiveEventParam;
use Imi\Server\Event\Listener\IReceiveEventListener;

/**
 * Receive事件后置处理
 * @ClassEventListener(className="Imi\Server\TcpServer\Server",eventName="receive",priority=PHP_INT_MIN)
 */
class AfterReceive implements IReceiveEventListener
{
    /**
     * 事件处理方法
     * @param ReceiveEventParam $e
     * @return void
     */
    public function handle(ReceiveEventParam $e)
    {
        // 销毁请求上下文
        RequestContext::destroy();
    }
}