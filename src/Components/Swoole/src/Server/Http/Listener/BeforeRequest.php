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
    /**
     * @var \Imi\Server\Http\Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * @var \Imi\Swoole\Server\Http\Server|\Imi\Swoole\Server\WebSocket\Server
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
