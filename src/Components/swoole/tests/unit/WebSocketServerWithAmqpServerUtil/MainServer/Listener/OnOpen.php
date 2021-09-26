<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\WebSocketServerWithAmqpServerUtil\MainServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\ConnectionContext;
use Imi\Swoole\Server\Event\Listener\IOpenEventListener;
use Imi\Swoole\Server\Event\Param\OpenEventParam;

/**
 * @ClassEventListener(className="Imi\Swoole\Server\WebSocket\Server",eventName="open")
 */
class OnOpen implements IOpenEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(OpenEventParam $e): void
    {
        ConnectionContext::set('requestUri', (string) $e->request->getUri());
    }
}
