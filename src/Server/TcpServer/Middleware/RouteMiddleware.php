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
class RouteMiddleware implements IMiddleware
{
    /**
     * 处理方法
     *
     * @param IReceiveData $data
     * @param IReceiveHandler $handle
     * @return void
     */
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        // 路由解析
        $route = RequestContext::getServerBean('TcpRoute');
        $result = $route->parse($data->getFormatData());
        if(null === $result || !is_callable($result['callable']))
        {
            // 未找到匹配的命令，TODO:处理
            
        }
        else
        {
            RequestContext::set('routeResult', $result);

            $middlewares = $result['middlewares'];
            $middlewares[] = ActionMiddleware::class;
            $handler = new ReceiveHandler($middlewares);
            return $handler->handle($frame);
        }
        return $handler->handle($data);
    }

}