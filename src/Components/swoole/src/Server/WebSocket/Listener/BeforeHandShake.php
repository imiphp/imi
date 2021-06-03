<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Listener;

use Imi\Bean\Annotation\ClassEventListener;
use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\IHandShakeEventListener;
use Imi\Swoole\Server\Event\Param\HandShakeEventParam;
use Imi\Swoole\SwooleWorker;
use Imi\Util\Http\Consts\StatusCode;

/**
 * HandShake事件前置处理.
 *
 * @ClassEventListener(className="Imi\Swoole\Server\WebSocket\Server",eventName="handShake",priority=Imi\Util\ImiPriority::IMI_MAX)
 */
class BeforeHandShake implements IHandShakeEventListener
{
    /**
     * 默认的 WebSocket 握手.
     */
    public function handle(HandShakeEventParam $e): void
    {
        $request = $e->request;
        $response = $e->response;
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $response->setStatus(StatusCode::SERVICE_UNAVAILABLE)->send();
            $e->stopPropagation();

            return;
        }

        // 中间件
        /** @var \Imi\Server\Http\Dispatcher $dispatcher */
        $dispatcher = RequestContext::getServerBean('HttpDispatcher');
        $dispatcher->dispatch($request);
    }
}
