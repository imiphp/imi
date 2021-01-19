<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\ConnectContext;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\IHandShakeEventListener;
use Imi\Swoole\Server\Event\Param\HandShakeEventParam;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Swoole\SwooleWorker;

/**
 * HandShake事件前置处理.
 *
 * @ClassEventListener(className="Imi\Swoole\Server\WebSocket\Server",eventName="handShake",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeHandShake implements IHandShakeEventListener
{
    /**
     * 默认的 WebSocket 握手.
     *
     * @param HandShakeEventParam $e
     *
     * @return void
     */
    public function handle(HandShakeEventParam $e)
    {
        $request = $e->request;
        $response = $e->response;
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $response->setStatus(StatusCode::SERVICE_UNAVAILABLE)->send();
            $e->stopPropagation();

            return;
        }
        // 上下文创建
        RequestContext::muiltiSet([
            'request'   => $request,
            'response'  => $response,
            'fd'        => RequestContext::get('swooleRequest')->fd,
        ]);

        // 连接上下文创建
        ConnectContext::create();

        // 中间件
        /** @var \Imi\Server\Http\Dispatcher $dispatcher */
        $dispatcher = RequestContext::getServerBean('HttpDispatcher');
        $dispatcher->dispatch($request, $response);
    }
}
