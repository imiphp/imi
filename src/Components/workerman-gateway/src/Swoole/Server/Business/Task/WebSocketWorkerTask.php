<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Business\Task;

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    class WebSocketWorkerTask extends WorkerTask
    {
        protected string $errorHandler = 'WebSocketErrorHandler';
    }
}
