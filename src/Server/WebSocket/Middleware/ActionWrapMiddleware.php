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
class ActionWrapMiddleware implements IMiddleware
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
        // 获取路由结果
        $result = RequestContext::get('routeResult');
        if(null === $result)
        {
            return $handler->handle($frame);
        }
        $middlewares = $result->routeItem->middlewares;
        $middlewares[] = ActionMiddleware::class;
        $handler = new MessageHandler($middlewares);
        return $handler->handle($frame);
    }

}