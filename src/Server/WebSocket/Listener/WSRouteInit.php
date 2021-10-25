<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\TMiddleware;
use Imi\Server\ServerManager;
use Imi\Server\WebSocket\Parser\WSControllerParser;
use Imi\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Server\WebSocket\Route\Annotation\WSMiddleware;
use Imi\Server\WebSocket\Route\Annotation\WSRoute;
use Imi\Worker;

/**
 * WebSocket 服务器路由初始化.
 */
class WSRouteInit implements IEventListener
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
        $controllerParser = WSControllerParser::getInstance();
        $context = RequestContext::getContext();
        foreach (ServerManager::getServers() as $name => $server)
        {
            if (Protocol::WEBSOCKET !== $server->getProtocol())
            {
                continue;
            }
            $context['server'] = $server;
            /** @var \Imi\Server\WebSocket\Route\WSRoute $route */
            $route = $server->getBean('WSRoute');
            foreach ($controllerParser->getByServer($name) as $className => $classItem)
            {
                /** @var \Imi\Server\WebSocket\Route\Annotation\WSController $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                if (null !== $classAnnotation->server && !\in_array($name, (array) $classAnnotation->server))
                {
                    continue;
                }
                // 类中间件
                $classMiddlewares = [];
                /** @var WSMiddleware $middleware */
                foreach (AnnotationManager::getClassAnnotations($className, WSMiddleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, WSAction::class) as $methodName => $actionAnnotations)
                {
                    /** @var \Imi\Server\WebSocket\Route\Annotation\WSRoute[] $routes */
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, WSRoute::class);
                    if (!$routes)
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    /** @var WSMiddleware $middleware */
                    foreach (AnnotationManager::getMethodAnnotations($className, $methodName, WSMiddleware::class) ?? [] as $middleware)
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));

                    foreach ($routes as $routeItem)
                    {
                        $routeItem = clone $routeItem;
                        // 方法上的 @WSRoute 未设置 route，则使用 @WSController 中的
                        if (null === $routeItem->route)
                        {
                            $routeItem->route = $classAnnotation->route;
                        }
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
