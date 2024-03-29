<?php

declare(strict_types=1);

namespace Imi\Workerman\Test\AppServer\TcpServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;
use Imi\Server\TcpServer\Middleware\IMiddleware;

#[Bean]
class Test implements IMiddleware
{
    /**
     * {@inheritDoc}
     */
    public function process(IReceiveData $data, IReceiveHandler $handler): mixed
    {
        RequestContext::set('middlewareData', 'imi');

        return $handler->handle($data);
    }
}
