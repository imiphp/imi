<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Swoole\Server\WebSocket\IMessageHandler;
use Imi\Swoole\Server\WebSocket\Message\IFrame;
use Imi\Swoole\Server\WebSocket\MessageHandler;

/**
 * @Bean("WebSocketActionWrapMiddleware")
 */
class ActionWrapMiddleware implements IMiddleware
{
    /**
     * 动作中间件.
     *
     * @var string
     */
    protected string $actionMiddleware = ActionMiddleware::class;

    /**
     * 处理方法.
     *
     * @param IFrame          $frame
     * @param IMessageHandler $handler
     *
     * @return void
     */
    public function process(IFrame $frame, IMessageHandler $handler)
    {
        // 获取路由结果
        $result = RequestContext::get('routeResult');
        if (null === $result)
        {
            return $handler->handle($frame);
        }
        $middlewares = $result->routeItem->middlewares;
        $middlewares[] = $this->actionMiddleware;
        $handler = new MessageHandler($middlewares);

        return $handler->handle($frame);
    }
}
