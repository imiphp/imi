<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer\Middleware;

use Imi\Swoole\Server\TcpServer\IReceiveHandler;
use Imi\Swoole\Server\TcpServer\Message\IReceiveData;

interface IMiddleware
{
    public function process(IReceiveData $data, IReceiveHandler $handler);
}
