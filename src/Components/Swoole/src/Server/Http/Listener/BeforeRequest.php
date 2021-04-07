<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Listener;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\Http\Dispatcher;
use Imi\Swoole\Server\Base;
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
    protected Base $server;

    /**
     * @param \Imi\Swoole\Server\Http\Server|\Imi\Swoole\Server\WebSocket\Server $server
     */
    public function __construct(Base $server)
    {
        $this->server = $server;
    }

    /**
     * 事件处理方法.
     */
    public function handle(RequestEventParam $e): void
    {
        $request = $e->request;
        $response = $e->response;
        $context = RequestContext::getContext();
        $server = $this->server;
        if ($server->isHttp2())
        {
            $context['clientId'] = $context['swooleRequest']->fd;
            ConnectContext::create();
        }
        // 中间件
        $this->dispatcher ??= $server->getBean('HttpDispatcher');
        $this->dispatcher->dispatch($request, $response);
    }
}
