<?php

namespace Imi\Server\Http\Listener;

use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Server\Event\Listener\IRequestEventListener;
use Imi\Server\Event\Param\RequestEventParam;

/**
 * request事件前置处理.
 */
class BeforeRequest implements IRequestEventListener
{
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
        /** @var \Imi\Server\Http\Server $server */
        $server = $context['server'];
        if ($server->isHttp2())
        {
            $context['fd'] = $context['swooleRequest']->fd;
            ConnectContext::create();
        }
        // 中间件
        /** @var \Imi\Server\Http\Dispatcher $dispatcher */
        $dispatcher = $server->getBean('HttpDispatcher');
        $dispatcher->dispatch($request, $response);
    }
}
