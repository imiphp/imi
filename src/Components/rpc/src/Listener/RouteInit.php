<?php

namespace Imi\Rpc\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Rpc\Contract\IRpcServer;
use Imi\Rpc\Route\Annotation\Parser\RpcControllerParser;
use Imi\Server\Route\RouteCallable;
use Imi\ServerManage;

/**
 * RPC 服务器路由初始化.
 *
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class RouteInit implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        $this->parseAnnotations($e);
    }

    /**
     * 处理注解路由.
     *
     * @return void
     */
    private function parseAnnotations(EventParam $e)
    {
        $controllerParser = RpcControllerParser::getInstance();
        foreach (ServerManage::getServers() as $name => $server)
        {
            if (!$server instanceof IRpcServer)
            {
                continue;
            }
            /** @var IRpcServer|\Imi\Server\Base $server */
            $controllerAnnotationClass = $server->getControllerAnnotation();
            $actionAnnotationClass = $server->getActionAnnotation();
            $routeAnnotationClass = $server->getRouteAnnotation();
            RequestContext::create();
            RequestContext::set('server', $server);
            /** @var \Imi\Rpc\Route\IRoute $route */
            $route = $server->getBean($server->getRouteClass());
            foreach ($controllerParser->getByServer($name, $controllerAnnotationClass) as $className => $classItem)
            {
                $classAnnotation = $classItem->getAnnotation();
                if (null !== $classAnnotation->server && !\in_array($name, (array) $classAnnotation->server))
                {
                    continue;
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, $actionAnnotationClass) as $methodName => $actionAnnotations)
                {
                    /** @var \Imi\Rpc\Route\Annotation\Contract\IRpcRoute[] $routes */
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, $routeAnnotationClass);
                    if (!isset($routes[0]))
                    {
                        $routes = [
                            $route->getDefaultRouteAnnotation($className, $methodName, $classAnnotation),
                        ];
                    }

                    foreach ($routes as $routeItem)
                    {
                        $route->addRuleAnnotation($classAnnotation, $routeItem, new RouteCallable($server, $className, $methodName), [
                            'serverName'    => $name,
                        ]);
                    }
                }
            }
            RequestContext::destroy();
        }
    }
}
