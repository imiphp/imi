<?php

namespace Imi\Test\WebSocketServer\MainServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\ConnectContext;
use Imi\Server\Event\Listener\IOpenEventListener;
use Imi\Server\Event\Param\OpenEventParam;

/**
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="open")
 */
class OnOpen implements IOpenEventListener
{
    /**
     * 事件处理方法.
     *
     * @param OpenEventParam $e
     *
     * @return void
     */
    public function handle(OpenEventParam $e)
    {
        ConnectContext::set('requestUri', (string) $e->request->getUri());
    }
}
