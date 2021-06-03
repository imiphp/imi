<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\ConnectContext;
use Imi\Swoole\Server\Event\Listener\IOpenEventListener;
use Imi\Swoole\Server\Event\Param\OpenEventParam;

/**
 * @ClassEventListener(className="Imi\WorkermanGateway\Swoole\Server\Business\WebSocketBusinessServer",eventName="open")
 */
class SwooleOnOpen implements IOpenEventListener
{
    /**
     * 事件处理方法.
     */
    public function handle(OpenEventParam $e): void
    {
        ConnectContext::set('requestUri', (string) $e->request->getUri());
    }
}
