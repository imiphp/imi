<?php

declare(strict_types=1);

namespace Imi\Server\Http\Listener;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\Base;
use Imi\Server\Event\Listener\IRequestEventListener;
use Imi\Server\Event\Param\RequestEventParam;
use Imi\Server\Http\Dispatcher;

/**
 * request事件前置处理.
 */
class BeforeRequest implements IRequestEventListener
{
    /**
     * @var \Imi\Server\Http\Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @var \Imi\Server\Http\Server|\Imi\Server\WebSocket\Server
     */
    protected Base $server;

    public function __construct(Base $server)
    {
        $this->server = $server;
    }

    /**
     * 事件处理方法.
     *
     * @param RequestEventParam $e
     *
     * @return void
     */
    public function handle(RequestEventParam $e)
    {
        $request = $e->request;
        $response = $e->response;
        $context = RequestContext::getContext();
        $server = $this->server;
        if ($server->isHttp2())
        {
            $context['fd'] = $context['swooleRequest']->fd;
            ConnectContext::create();
        }
        // 中间件
        if (!isset($this->dispatcher))
        {
            $this->dispatcher = $server->getBean('HttpDispatcher');
        }
        $this->dispatcher->dispatch($request, $response);
    }
}
