<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Test\AppServer\WebSocketServer\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\ConnectionContext;
use Imi\Swoole\Server\Event\Listener\IOpenEventListener;
use Imi\Swoole\Server\Event\Param\OpenEventParam;

if (\extension_loaded('swoole'))
{
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
            ConnectionContext::set('requestUri', (string) $e->request->getUri());
        }
    }
}
