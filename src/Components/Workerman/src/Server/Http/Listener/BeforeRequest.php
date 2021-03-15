<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Http\Listener;

use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Server\Http\Dispatcher;
use Imi\Workerman\Server\Http\Server;

/**
 * request事件前置处理.
 */
class BeforeRequest implements IEventListener
{
    /**
     * @var \Imi\Server\Http\Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @var Server
     */
    protected Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
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
