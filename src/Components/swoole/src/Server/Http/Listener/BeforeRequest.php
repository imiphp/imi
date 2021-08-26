<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Listener;

use Imi\ConnectionContext;
use Imi\RequestContext;
use Imi\Server\Http\Dispatcher;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Listener\IRequestEventListener;
use Imi\Swoole\Server\Event\Param\RequestEventParam;

/**
 * request事件前置处理.
 */
class BeforeRequest implements IRequestEventListener
{
    protected Dispatcher $dispatcher;

    /**
     * @var \Imi\Swoole\Server\Http\Server|\Imi\Swoole\Server\WebSocket\Server
     */
    protected ISwooleServer $server;

    /**
     * @param \Imi\Swoole\Server\Http\Server|\Imi\Swoole\Server\WebSocket\Server $server
     */
    public function __construct(ISwooleServer $server)
    {
        $this->server = $server;
    }

    /**
     * 事件处理方法.
     */
    public function handle(RequestEventParam $e): void
    {
        $server = $this->server;
        if ($server->isHttp2())
        {
            $context = RequestContext::getContext();
            $context['clientId'] = $context['swooleRequest']->fd;
            ConnectionContext::create();
        }
        // 中间件
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->dispatcher ??= $server->getBean('HttpDispatcher');
        $dispatcher->dispatch($e->request);
    }
}
