<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\Http\Dispatcher;

/**
 * request事件前置处理.
 */
class BeforeRequest implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        ['request' => $request] = $e->getData();
        // 中间件
        /** @var Dispatcher $dispatcher */
        $dispatcher = RequestContext::getServerBean('HttpDispatcher');
        $dispatcher->dispatch($request);
    }
}
