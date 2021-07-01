<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\UdpServer\Controller\UdpController;
use Imi\Server\UdpServer\IPacketHandler;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean("UDPActionMiddleware")
 */
class ActionMiddleware implements IMiddleware
{
    /**
     * 处理方法.
     *
     * @return mixed
     */
    public function process(IPacketData $data, IPacketHandler $handler)
    {
        $requestContext = RequestContext::getContext();
        // 获取路由结果
        /** @var \Imi\Server\UdpServer\Route\RouteResult|null $result */
        $result = $requestContext['routeResult'] ?? null;
        if (null === $result)
        {
            return $handler->handle($data);
        }
        $callable = &$result->callable;
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = \is_array($callable) && isset($callable[0]) && $callable[0] instanceof UdpController;
        if ($isObject)
        {
            $callable[0]->server = $requestContext['server'] ?? null;
            $callable[0]->data = $data;
        }
        // 执行动作
        $actionResult = ($callable)($data->getFormatData());

        $requestContext['udpResult'] = $actionResult;

        $actionResult = $handler->handle($data);

        if (null !== $actionResult)
        {
            $requestContext['udpResult'] = $actionResult;
        }

        return $requestContext['udpResult'];
    }
}
