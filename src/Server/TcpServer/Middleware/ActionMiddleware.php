<?php

namespace Imi\Server\TcpServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\Controller\TcpController;
use Imi\RequestContext;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * @Bean("TCPActionMiddleware")
 */
class ActionMiddleware implements IMiddleware
{
    /**
     * 处理方法.
     *
     * @param IReceiveData    $data
     * @param IReceiveHandler $handler
     *
     * @return mixed
     */
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        $requestContext = RequestContext::getContext();
        // 获取路由结果
        /** @var \Imi\Server\TcpServer\Route\RouteResult|null $result */
        $result = $requestContext['routeResult'] ?? null;
        if (null === $result)
        {
            return $handler->handle($data);
        }
        $callable = &$result->callable;
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = \is_array($callable) && isset($callable[0]) && $callable[0] instanceof TcpController;
        if ($isObject)
        {
            if (!$result->routeItem->singleton)
            {
                // 复制一份控制器对象
                $callable[0] = clone $callable[0];
            }
            $callable[0]->server = RequestContext::getServer();
            $callable[0]->data = $data;
        }
        // 执行动作
        $actionResult = ($callable)($data->getFormatData());

        $requestContext['tcpResult'] = $actionResult;

        $actionResult = $handler->handle($data);

        if (null !== $actionResult)
        {
            $requestContext['tcpResult'] = $actionResult;
        }

        return $requestContext['tcpResult'];
    }
}
