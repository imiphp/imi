<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Swoole\Server\Business\Task;

if (\Imi\Util\Imi::checkAppType('swoole'))
{
    class TcpWorkerTask extends WorkerTask
    {
        protected string $errorHandler = 'TcpErrorHandler';
    }
}
