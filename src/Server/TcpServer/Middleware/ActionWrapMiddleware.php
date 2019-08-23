<?php
namespace Imi\Server\TcpServer\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\TcpServer\ReceiveHandler;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * @Bean
 */
class ActionWrapMiddleware implements IMiddleware
{
    /**
     * 处理方法
     *
     * @param IReceiveData $frame
     * @param IReceiveHandler $handler
     * @return void
     */
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        // 获取路由结果
        $result = RequestContext::get('routeResult');
        if(null === $result)
        {
            return $handler->handle($data);
        }
        $middlewares = $result->routeItem->middlewares;
        $middlewares[] = ActionMiddleware::class;
        $handler = new ReceiveHandler($middlewares);
        return $handler->handle($data);
    }

}