<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\Worker;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\HandShakeEventParam;
use Imi\Server\Event\Listener\IHandShakeEventListener;

/**
 * HandShake事件前置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="handShake",priority=PHP_INT_MAX)
 */
class BeforeHandShake implements IHandShakeEventListener
{
    /**
     * 默认的 WebSocket 握手
     * @param RequestEventParam $e
     * @return void
     */
    public function handle(HandShakeEventParam $e)
    {
        if(!Worker::isInited())
        {
            $GLOBALS['WORKER_START_END_RESUME_COIDS'][] = Coroutine::getuid();
            Coroutine::suspend();
        }
        // 上下文创建
        RequestContext::create();
        RequestContext::set('server', $e->request->getServerInstance());
        RequestContext::set('request', $e->request);
        RequestContext::set('response', $e->response);
        RequestContext::set('fd', $e->request->getSwooleRequest()->fd);

        // 中间件
        $dispatcher = RequestContext::getServerBean('HttpDispatcher');
        $dispatcher->dispatch($e->request, $e->response);

        // 连接上下文创建
        ConnectContext::create();

        // http 路由解析结果
        $routeResult = RequestContext::get('routeResult');
        unset($routeResult['callable']);
        ConnectContext::set('httpRouteResult', $routeResult);

        // 释放请求上下文
        RequestContext::destroy();
    }

}