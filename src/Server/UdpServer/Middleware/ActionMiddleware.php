<?php
namespace Imi\Server\UdpServer\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Controller\UdpController;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean("UDPActionMiddleware")
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
        $requestContext = RequestContext::getContext();
        // 获取路由结果
        /** @var \Imi\Server\UdpServer\Route\RouteResult $result */
        $result = $requestContext['routeResult'] ?? null;
        if(null === $result)
        {
            return $handler->handle($data);
        }
        $callable = &$result->callable;
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = is_array($callable) && isset($callable[0]) && $callable[0] instanceof UdpController;
        if($isObject)
        {
            if(!$result->routeItem->singleton)
            {
                // 复制一份控制器对象
                $callable[0] = clone $callable[0];
            }
            $callable[0]->server = RequestContext::getServer();
            $callable[0]->data = $data;
        }
        // 执行动作
        $actionResult = ($callable)($data->getFormatData());

        $requestContext['udpResult'] = $actionResult;

        $actionResult = $handler->handle($data);

        if(null !== $actionResult)
        {
            $requestContext['udpResult'] = $actionResult;
        }

        return $requestContext['udpResult'];
    }
    
}