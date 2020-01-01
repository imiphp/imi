<?php
namespace Imi\Server\WebSocket\Listener;

use Imi\Worker;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Util\Coroutine;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Server\Event\Param\OpenEventParam;
use Imi\Bean\Annotation\ClassEventListener;
use Imi\Server\Event\Param\HandShakeEventParam;
use Imi\Server\Event\Listener\IHandShakeEventListener;

/**
 * HandShake事件前置处理
 * @ClassEventListener(className="Imi\Server\WebSocket\Server",eventName="handShake",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeHandShake implements IHandShakeEventListener
{
    /**
     * 默认的 WebSocket 握手
     * @param HandShakeEventParam $e
     * @return void
     */
    public function handle(HandShakeEventParam $e)
    {
        if(!Worker::isWorkerStartAppComplete())
        {
            $e->response->withStatus(StatusCode::SERVICE_UNAVAILABLE)->send();
            $e->stopPropagation();
            return;
        }
        // 上下文创建
        RequestContext::muiltiSet([
            'server'    =>  $e->request->getServerInstance(),
            'request'   =>  $e->request,
            'response'  =>  $e->response,
            'fd'        =>  $e->request->getSwooleRequest()->fd,
        ]);

        // 连接上下文创建
        ConnectContext::create();

        // 中间件
        $dispatcher = RequestContext::getServerBean('HttpDispatcher');
        $response = $dispatcher->dispatch($e->request, $e->response);
        if(StatusCode::SWITCHING_PROTOCOLS === $response->getStatusCode())
        {
            // http 路由解析结果
            $routeResult = RequestContext::get('routeResult');
            $routeResult->routeItem->callable = null;
            $routeResult->callable = null;
            ConnectContext::muiltiSet([
                'httpRouteResult'   =>  $routeResult,
                'uri'               =>  $e->request->getUri(),
            ]);

            $server = $e->getTarget();
            $request = $e->request;
            $server->trigger('open', [
                'server'   => &$server,
                'request'  => &$request,
            ], $this, OpenEventParam::class);
        }
    }

}