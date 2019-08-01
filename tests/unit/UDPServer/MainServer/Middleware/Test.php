<?php
namespace Imi\Test\UDPServer\MainServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\Server\UDPServer\IReceiveHandler;
use Imi\Server\UDPServer\Message\IReceiveData;
use Imi\Server\UDPServer\Middleware\IMiddleware;

/**
 * @Bean
 */
class Test implements IMiddleware
{
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        var_dump('test middleware');
        return $handler->handle($data, $handler);
    }
}