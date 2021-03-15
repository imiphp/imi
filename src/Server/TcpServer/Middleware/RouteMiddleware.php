<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\TcpServer\Error\ITcpRouteNotFoundHandler;
use Imi\Server\TcpServer\IReceiveHandler;
use Imi\Server\TcpServer\Message\IReceiveData;
use Imi\Server\TcpServer\Route\TcpRoute;

/**
 * @Bean("TCPRouteMiddleware")
 */
class RouteMiddleware implements IMiddleware
{
    /**
     * @ServerInject("TcpRoute")
     *
     * @var \Imi\Server\TcpServer\Route\TcpRoute
     */
    protected TcpRoute $route;

    /**
     * @ServerInject("TcpRouteNotFoundHandler")
     *
     * @var \Imi\Server\TcpServer\Error\ITcpRouteNotFoundHandler
     */
    protected ITcpRouteNotFoundHandler $notFoundHandler;

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
        // 路由解析
        $result = $this->route->parse($data->getFormatData());
        if (null === $result || !\is_callable($result->callable))
        {
            // 未匹配到路由
            $result = $this->notFoundHandler->handle($data, $handler);
        }
        else
        {
            RequestContext::set('routeResult', $result);
            $result = $handler->handle($data);
        }

        return $result;
    }
}
