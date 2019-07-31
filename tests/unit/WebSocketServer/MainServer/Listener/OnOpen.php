<?php
namespace Imi\Test\WebSocketServer\MainServer\Listener;

use Imi\Server\Event\Param\OpenEventParam;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Listener\IOpenEventListener;

/**
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="open")
 */
class OnOpen implements IOpenEventListener
{
    /**
     * 事件处理方法
     * @param OpenEventParam $e
     * @return void
     */
    public function handle(OpenEventParam $e)
    {
        // var_dump('open');
        // var_dump($e->request->getHeaders());
    }
}