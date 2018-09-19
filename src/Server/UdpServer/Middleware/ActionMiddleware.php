<?php
namespace Imi\Server\UdpServer\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Controller\UdpController;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean
 */
class ActionMiddleware implements IMiddleware
{
    /**
     * 处理方法
     *
     * @param IReceiveData $data
     * @param IReceiveHandler $handle
     * @return void
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        // 获取路由结果
        $result = RequestContext::get('routeResult');
        if(null === $result)
        {
            return $handler->handle($data);
        }
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = is_array($result['callable']) && isset($result['callable'][0]) && $result['callable'][0] instanceof UdpController;
        if($isObject)
        {
            // 复制一份控制器对象
            $result['callable'][0] = clone $result['callable'][0];
            $result['callable'][0]->server = RequestContext::getServer();
            $result['callable'][0]->data = $data;
        }
        // 执行动作
        $actionResult = call_user_func($result['callable'], $data->getFormatData());

        RequestContext::set('udpResult', $actionResult);

        $actionResult = $handler->handle($data);

        if(null !== $actionResult)
        {
            RequestContext::set('udpResult', $actionResult);
        }

        return RequestContext::get('udpResult');
    }
    
}