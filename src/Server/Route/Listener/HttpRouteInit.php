<?php

namespace Imi\Server\Route\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Server\Http\Route\HttpRoute;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\Middleware;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Annotation\WebSocket\WSConfig;
use Imi\Server\Route\Parser\ControllerParser;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\TMiddleware;
use Imi\ServerManage;
use Imi\Worker;

/**
 * http服务器路由初始化.
 *
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class HttpRouteInit implements IEventListener
{
    use TMiddleware;

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
        $this->parseConfigs();
    }

    /**
     * 处理注解路由.
     *
     * @return void
     */
    private function parseAnnotations(EventParam $e)
    {
        $controllerParser = ControllerParser::getInstance();
        $context = RequestContext::getContext();
        foreach (ServerManage::getServers() as $name => $server)
        {
            if (!$server instanceof \Imi\Server\Http\Server && !$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            $context['server'] = $server;
            /** @var HttpRoute $route */
            $route = $server->getBean('HttpRoute');
            $autoEndSlash = $route->getAutoEndSlash();
            foreach ($controllerParser->getByServer($name) as $className => $classItem)
            {
                /** @var \Imi\Server\Route\Annotation\Controller $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                // 类中间件
                $classMiddlewares = [];
                foreach (AnnotationManager::getClassAnnotations($className, Middleware::class) ?? [] as $middleware)
                {
                    /** @var Middleware $middleware */
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
                    foreach (AnnotationManager::getMethodAnnotations($className, $methodName, Middleware::class) ?? [] as $middleware)
                    {
                        /** @var Middleware $middleware */
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
                        if ((!isset($routeItem->url[0]) || '/' !== $routeItem->url[0]) && '' != $prefix)
                        {
                            $routeItem->url = $prefix . $routeItem->url;
                        }
                        $routeCallable = new RouteCallable($server, $className, $methodName);
                        $options = [
                            'middlewares'   => $middlewares,
                            'wsConfig'      => AnnotationManager::getMethodAnnotations($className, $methodName, WSConfig::class)[0] ?? null,
                            'singleton'     => null === $classAnnotation->singleton ? Config::get('@server.' . $name . '.controller.singleton', false) : $classAnnotation->singleton,
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
            if (0 === Worker::getWorkerID())
            {
                $route->checkDuplicateRoutes();
            }
            unset($context['server']);
        }
    }

    /**
     * 处理配置文件路由.
     *
     * @return void
     */
    private function parseConfigs()
    {
        $context = RequestContext::getContext();
        foreach (ServerManage::getServers() as $server)
        {
            if (!$server instanceof \Imi\Server\Http\Server && !$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            $context['server'] = $server;
            $route = $server->getBean('HttpRoute');
            foreach (Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $routeOption)
            {
                $routeAnnotation = new Route($routeOption['route'] ?? []);
                if (isset($routeOption['callback']))
                {
                    $callable = $routeOption['callback'];
                }
                else
                {
                    $callable = new RouteCallable($server, $routeOption['controller'], $routeOption['method']);
                }
                $route->addRuleAnnotation($routeAnnotation, $callable, [
                    'middlewares' => $routeOption['middlewares'] ?? [],
                ]);
            }
            unset($context['server']);
        }
    }
}
