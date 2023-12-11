<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\Http\Dispatcher;
use Imi\Workerman\Server\Http\Event\WorkermanHttpRequestEvent;

/**
 * request事件前置处理.
 */
class BeforeRequest implements IEventListener
{
    /**
     * @param WorkermanHttpRequestEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        // 中间件
        /** @var Dispatcher $dispatcher */
        $dispatcher = RequestContext::getServerBean('HttpDispatcher');
        $dispatcher->dispatch($e->request);
    }
}
