<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\Listener;

use Imi\RequestContext;
use Imi\Swoole\Server\Event\Listener\ICloseEventListener;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\SwooleWorker;

class Http2BeforeClose implements ICloseEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(CloseEventParam $e): void
    {
        if (!SwooleWorker::isWorkerStartAppComplete())
        {
            $e->stopPropagation();

            return;
        }
        RequestContext::muiltiSet([
            'clientId'        => $e->clientId,
            'server'          => $e->getTarget(),
        ]);
    }
}
