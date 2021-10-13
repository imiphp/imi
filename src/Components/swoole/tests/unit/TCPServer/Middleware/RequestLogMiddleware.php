<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\TCPServer\Middleware;

use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;
use Imi\Server\TcpServer\Middleware\IMiddleware;

class RequestLogMiddleware implements IMiddleware
{
    /**
     * {@inheritDoc}
     */
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        Log::info('Server: ' . RequestContext::getServer()->getName() . ', Url: ' . var_export($data->getFormatData(), true));

        return $handler->handle($data);
    }
}
