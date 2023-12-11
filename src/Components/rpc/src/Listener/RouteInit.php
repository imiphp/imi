<?php

declare(strict_types=1);

namespace Imi\Rpc\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Rpc\Contract\IRpcServer;
use Imi\Rpc\Route\Annotation\Parser\RpcControllerParser;
use Imi\Server\ServerManager;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Util\DelayServerBeanCallable;

/**
 * RPC 服务器路由初始化.
 */
#[Listener(eventName: SwooleEvents::SERVER_WORKER_START, one: true)]
class RouteInit implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        $this->parseAnnotations($e);
    }

    /**
     * 处理注解路由.
     */
    private function parseAnnotations(\Imi\Event\Contract\IEvent $e): void
    {
        $controllerParser = RpcControllerParser::getInstance();
        foreach (ServerManager::getServers() as $name => $server)
        {
            if (!$server instanceof IRpcServer)
            {
                continue;
            }
            /** @var IRpcServer|\Imi\Swoole\Server\Base $server */
            $controllerAnnotationClass = $server->getControllerAnnotation();
            $actionAnnotationClass = $server->getActionAnnotation();
            $routeAnnotationClass = $server->getRouteAnnotation();
            $contextRequest = RequestContext::create();
            $contextRequest['server'] = $server;
            /** @var \Imi\Rpc\Route\IRoute $route */
            $route = $server->getBean($server->getRouteClass());
            foreach ($controllerParser->getByServer($name, $controllerAnnotationClass) as $className => $classItem)
            {
                $classAnnotation = $classItem->getAnnotation();
                if (null !== $classAnnotation->server && !\in_array($name, (array) $classAnnotation->server))
                {
                    continue;
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, $actionAnnotationClass) as $methodName => $_)
                {
                    /** @var \Imi\Rpc\Route\Annotation\Contract\IRpcRoute[] $routes */
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, $routeAnnotationClass);
                    if (!$routes)
                    {
                        $routes = [
                            $route->getDefaultRouteAnnotation($className, $methodName, $classAnnotation),
                        ];
                    }

                    foreach ($routes as $routeItem)
                    {
                        $route->addRuleAnnotation($classAnnotation, $routeItem, new DelayServerBeanCallable($server, $className, $methodName), [
                            'serverName'    => $name,
                        ]);
                    }
                }
            }
            RequestContext::destroy();
        }
    }
}
