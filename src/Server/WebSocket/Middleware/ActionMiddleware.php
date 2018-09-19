<?php
namespace Imi\Server\WebSocket\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Controller\WebSocketController;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\WebSocket\IMessageHandler;

/**
 * @Bean
 */
class ActionMiddleware implements IMiddleware
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
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = is_array($result['callable']) && isset($result['callable'][0]) && $result['callable'][0] instanceof WebSocketController;
        if($isObject)
        {
            // 复制一份控制器对象
            $result['callable'][0] = clone $result['callable'][0];
            $result['callable'][0]->server = RequestContext::getServer();
            $result['callable'][0]->frame = $frame;
        }
        // 执行动作
        $actionResult = call_user_func($result['callable'], $frame->getFormatData());

        RequestContext::set('wsResult', $actionResult);

        $actionResult = $handler->handle($frame);

        if(null !== $actionResult)
        {
            RequestContext::set('wsResult', $actionResult);
        }

        return RequestContext::get('wsResult');
    }
    
}