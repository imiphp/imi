<?php
namespace Imi\Server\TcpServer\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Controller\TcpController;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * @Bean("TCPActionMiddleware")
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
    public function process(IReceiveData $data, IReceiveHandler $handler)
    {
        // 获取路由结果
        /** @var \Imi\Server\TcpServer\Route\RouteResult $result */
        $result = RequestContext::get('routeResult');
        if(null === $result)
        {
            return $handler->handle($data);
        }
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = is_array($result->callable) && isset($result->callable[0]) && $result->callable[0] instanceof TcpController;
        if($isObject)
        {
            if(!$result->routeItem->singleton)
            {
                // 复制一份控制器对象
                $result->callable[0] = clone $result->callable[0];
            }
            $result->callable[0]->server = RequestContext::getServer();
            $result->callable[0]->data = $data;
        }
        // 执行动作
        $actionResult = ($result->callable)($data->getFormatData());

        RequestContext::set('tcpResult', $actionResult);

        $actionResult = $handler->handle($data);

        if(null !== $actionResult)
        {
            RequestContext::set('tcpResult', $actionResult);
        }

        return RequestContext::get('tcpResult');
    }
    
}