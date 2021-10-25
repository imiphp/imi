<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\TMiddleware;
use Imi\Server\ServerManager;
use Imi\Server\TcpServer\Contract\ITcpServer;
use Imi\Server\TcpServer\Parser\TcpControllerParser;
use Imi\Server\TcpServer\Route\Annotation\TcpAction;
use Imi\Server\TcpServer\Route\Annotation\TcpMiddleware;
use Imi\Server\TcpServer\Route\Annotation\TcpRoute;
use Imi\Worker;

/**
 * TCP 服务器路由初始化.
 */
class TcpRouteInit implements IEventListener
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
        $controllerParser = TcpControllerParser::getInstance();
        $context = RequestContext::getContext();
        foreach (ServerManager::getServers(ITcpServer::class) as $name => $server)
        {
            $context['server'] = $server;
            /** @var \Imi\Server\TcpServer\Route\TcpRoute $route */
            $route = $server->getBean('TcpRoute');
            foreach ($controllerParser->getByServer($name) as $className => $classItem)
            {
                // 类中间件
                /** @var \Imi\Server\TcpServer\Route\Annotation\TcpController $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                if (null !== $classAnnotation->server && !\in_array($name, (array) $classAnnotation->server))
                {
                    continue;
                }
                $classMiddlewares = [];
                /** @var TcpMiddleware $middleware */
                foreach (AnnotationManager::getClassAnnotations($className, TcpMiddleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, TcpAction::class) as $methodName => $actionAnnotations)
                {
                    /** @var TcpRoute[] $routes */
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, TcpRoute::class);
                    if (!$routes)
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    /** @var TcpMiddleware $middleware */
                    foreach (AnnotationManager::getMethodAnnotations($className, $methodName, TcpMiddleware::class) ?? [] as $middleware)
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));

                    foreach ($routes as $routeItem)
                    {
                        $route->addRuleAnnotation($routeItem, new RouteCallable($server->getName(), $className, $methodName), [
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
