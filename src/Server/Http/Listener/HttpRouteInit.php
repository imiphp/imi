<?php

declare(strict_types=1);

namespace Imi\Server\Http\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Server\Http\Parser\ControllerParser;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Middleware;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Server\Protocol;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\TMiddleware;
use Imi\Server\ServerManager;
use Imi\Server\WebSocket\Route\Annotation\WSConfig;
use Imi\Worker;

/**
 * http服务器路由初始化.
 */
class HttpRouteInit implements IEventListener
{
    use TMiddleware;

    /**
     * 事件处理方法.
     */
    public function handle(EventParam $e): void
    {
        $this->parseAnnotations();
        $this->parseConfigs();
    }

    /**
     * 处理注解路由.
     */
    protected function parseAnnotations(): void
    {
        $controllerParser = ControllerParser::getInstance();
        $context = RequestContext::getContext();
        foreach (ServerManager::getServers() as $name => $server)
        {
            if (!\in_array($server->getProtocol(), [Protocol::HTTP, Protocol::WEBSOCKET]))
            {
                continue;
            }
            $context['server'] = $server;
            /** @var HttpRoute $route */
            $route = $server->getBean('HttpRoute');
            $autoEndSlash = $route->getAutoEndSlash();
            foreach ($controllerParser->getByServer($name) as $className => $classItem)
            {
                /** @var \Imi\Server\Http\Route\Annotation\Controller $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                if (null !== $classAnnotation->server && !\in_array($name, (array) $classAnnotation->server))
                {
                    continue;
                }
                // 类中间件
                $classMiddlewares = [];
                foreach (AnnotationManager::getClassAnnotations($className, Middleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, Action::class) as $methodName => $actionAnnotations)
                {
                    $routeAnnotations = AnnotationManager::getMethodAnnotations($className, $methodName, Route::class);
                    if (isset($routeAnnotations[0]))
                    {
                        $routes = $routeAnnotations;
                    }
                    else
                    {
                        $routes = [
                            new Route([
                                'url' => $methodName,
                            ]),
                        ];
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    /** @var Middleware $middleware */
                    foreach (AnnotationManager::getMethodAnnotations($className, $methodName, Middleware::class) ?? [] as $middleware)
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));

                    /** @var Route[] $routes */
                    foreach ($routes as $routeItem)
                    {
                        if (null === $routeItem->url)
                        {
                            $routeItem->url = $methodName;
                        }
                        $prefix = $classAnnotation->prefix;
                        if ('' != $prefix)
                        {
                            if ((!isset($routeItem->url[0]) || '/' !== $routeItem->url[0]))
                            {
                                if (isset($routeItem->url[1]) && './' === substr($routeItem->url, 0, 2))
                                {
                                    $prefixHasSlash = '/' === ($prefix[-1] ?? '');
                                    $routeItem->url = $prefix . substr($routeItem->url, $prefixHasSlash ? 2 : 1);
                                }
                                else
                                {
                                    $routeItem->url = $prefix . $routeItem->url;
                                }
                            }
                        }
                        $routeCallable = new RouteCallable($server->getName(), $className, $methodName);
                        $options = [
                            'middlewares'   => $middlewares,
                            'wsConfig'      => AnnotationManager::getMethodAnnotations($className, $methodName, WSConfig::class)[0] ?? null,
                        ];
                        $route->addRuleAnnotation($routeItem, $routeCallable, $options);
                        if (($routeItem->autoEndSlash || ($autoEndSlash && null === $routeItem->autoEndSlash)) && '/' !== substr($routeItem->url, 0, -1))
                        {
                            $routeItem = clone $routeItem;
                            $routeItem->url .= '/';
                            $route->addRuleAnnotation($routeItem, $routeCallable, $options);
                        }
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

    /**
     * 处理配置文件路由.
     */
    protected function parseConfigs(): void
    {
        $context = RequestContext::getContext();
        foreach (ServerManager::getServers() as $server)
        {
            if (Protocol::HTTP !== $server->getProtocol())
            {
                continue;
            }
            $context['server'] = $server;
            $route = $server->getBean('HttpRoute');
            $main = Helper::getMain($server->getConfig()['namespace']);
            if ($main)
            {
                foreach ($main->getConfig()['route'] ?? [] as $routeOption)
                {
                    $routeAnnotation = new Route($routeOption['route'] ?? []);
                    if (isset($routeOption['callback']))
                    {
                        $callable = $routeOption['callback'];
                    }
                    else
                    {
                        $callable = new RouteCallable($server->getName(), $routeOption['controller'], $routeOption['method']);
                    }
                    $route->addRuleAnnotation($routeAnnotation, $callable, [
                        'middlewares' => $routeOption['middlewares'] ?? [],
                    ]);
                }
            }
            unset($context['server']);
        }
    }
}
