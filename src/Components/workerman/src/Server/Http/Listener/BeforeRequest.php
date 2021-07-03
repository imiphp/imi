<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\Http\Dispatcher;
use Imi\Workerman\Server\Base as BaseServer;

/**
 * request事件前置处理.
 */
class BeforeRequest implements IEventListener
{
    protected Dispatcher $dispatcher;

    protected BaseServer $server;

    public function __construct(BaseServer $server)
    {
        $this->server = $server;
    }

    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        ['request' => $request,'response' => $response] = $e->getData();
        $server = $this->server;
        // 中间件
        $this->dispatcher ??= $server->getBean('HttpDispatcher');
        $this->dispatcher->dispatch($request);
    }
}
