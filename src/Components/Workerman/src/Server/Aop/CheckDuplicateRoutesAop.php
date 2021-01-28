<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\Aop;

use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\PointCutType;
use Imi\RequestContext;
use Imi\Workerman\Server\Contract\IWorkermanServer;

/**
 * @Aspect
 */
class CheckDuplicateRoutesAop
{
    /**
     * 过滤方法参数.
     *
     * @PointCut(
     *      type=PointCutType::METHOD,
     *      allow={
     *          "Imi\Server\Http\Route\HttpRoute::checkDuplicateRoutes",
     *          "Imi\Server\WebSocket\Route\WSRoute::checkDuplicateRoutes",
     *          "Imi\Server\TcpServer\Route\TcpRoute::checkDuplicateRoutes",
     *          "Imi\Server\UdpServer\Route\UdpRoute::checkDuplicateRoutes",
     *      }
     * )
     * @Around
     *
     * @return mixed
     */
    public function parse(AroundJoinPoint $joinPoint)
    {
        /** @var IWorkermanServer $server */
        $server = RequestContext::getServer();
        /** @var \Workerman\Worker $worker */
        $worker = RequestContext::get('worker');
        if ($server->getName() === $worker->name && 0 === $worker->id)
        {
            return $joinPoint->proceed();
        }
    }
}
