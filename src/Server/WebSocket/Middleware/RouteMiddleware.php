<?php
namespace Imi\Server\WebSocket\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\MessageHandler;
use Imi\Server\WebSocket\IMessageHandler;

/**
 * @Bean
 */
class RouteMiddleware implements IMiddleware
{
    /**
     * 处理方法
     *
     * @param IFrame $frame
     * @param IMessageHandler $handler
     * @return void
     */
    public function process(IFrame $frame, IMessageHandler $handler)
    {
        // 路由解析
        $route = RequestContext::getServerBean('WSRoute');
        $result = $route->parse($frame->getFormatData());
        if(null === $result || !is_callable($result->callable))
        {
            // 未找到匹配的命令，TODO:处理
            
        }
        else
        {
            RequestContext::set('routeResult', $result);

            $middlewares = $result->routeItem->middlewares;
            $middlewares[] = ActionMiddleware::class;
            $handler = new MessageHandler($middlewares);
            return $handler->handle($frame);
        }
        return $handler->handle($frame);
    }

}