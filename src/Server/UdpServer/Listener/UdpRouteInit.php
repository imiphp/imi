<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Server\Route\TMiddleware;
use Imi\Server\ServerManager;
use Imi\Server\UdpServer\Parser\UdpControllerParser;
use Imi\Server\UdpServer\Route\Annotation\UdpAction;
use Imi\Server\UdpServer\Route\Annotation\UdpMiddleware;
use Imi\Server\UdpServer\Route\Annotation\UdpRoute;
use Imi\Util\DelayServerBeanCallable;
use Imi\Worker;

/**
 * UDP 服务器路由初始化.
 */
class UdpRouteInit implements IEventListener
{
    use TMiddleware;

    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $this->parseAnnotations($e);
    }

    /**
     * 处理注解路由.
     */
    private function parseAnnotations(EventParam $e): void
    {
        $controllerParser = UdpControllerParser::getInstance();
        $context = RequestContext::getContext();
        foreach (ServerManager::getServers() as $name => $server)
        {
            if (Protocol::UDP !== $server->getProtocol())
            {
                continue;
            }
            $context['server'] = $server;
            /** @var \Imi\Server\UdpServer\Route\UdpRoute $route */
            $route = $server->getBean('UdpRoute');
            foreach ($controllerParser->getByServer($name) as $className => $classItem)
            {
                /** @var \Imi\Server\UdpServer\Route\Annotation\UdpController $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                if (null !== $classAnnotation->server && !\in_array($name, (array) $classAnnotation->server))
                {
                    continue;
                }
                // 类中间件
                $classMiddlewares = [];
                /** @var UdpMiddleware $middleware */
                foreach (AnnotationManager::getClassAnnotations($className, UdpMiddleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, UdpAction::class) as $methodName => $methodItem)
                {
                    /** @var UdpRoute[] $routes */
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, UdpRoute::class);
                    if (!$routes)
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    /** @var UdpMiddleware $middleware */
                    foreach (AnnotationManager::getMethodAnnotations($className, $methodName, UdpMiddleware::class) ?? [] as $middleware)
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));

                    foreach ($routes as $routeItem)
                    {
                        $route->addRuleAnnotation($routeItem, new DelayServerBeanCallable($server, $className, $methodName, [$server]), [
                            'middlewares' => $middlewares,
                        ]);
                    }
                }
            }
            if (0 === Worker::getWorkerId())
            {
                $route->checkDuplicateRoutes();
            }
            unset($context['server']);
        }
    }
}
