<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket\Listener;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Main\Helper;
use Imi\RequestContext;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\TMiddleware;
use Imi\ServerManage;
use Imi\Swoole\Server\WebSocket\Parser\WSControllerParser;
use Imi\Swoole\Server\WebSocket\Route\Annotation\WSAction;
use Imi\Swoole\Server\WebSocket\Route\Annotation\WSMiddleware;
use Imi\Swoole\Server\WebSocket\Route\Annotation\WSRoute;
use Imi\Swoole\Worker;

/**
 * WebSocket 服务器路由初始化.
 *
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class WSRouteInit implements IEventListener
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
        $controllerParser = WSControllerParser::getInstance();
        $context = RequestContext::getContext();
        foreach (ServerManage::getServers() as $name => $server)
        {
            if (!$server instanceof \Imi\Swoole\Server\WebSocket\Server)
            {
                continue;
            }
            $context['server'] = $server;
            /** @var \Imi\Swoole\Server\WebSocket\Route\WSRoute $route */
            $route = $server->getBean('WSRoute');
            foreach ($controllerParser->getByServer($name) as $className => $classItem)
            {
                /** @var \Imi\Swoole\Server\WebSocket\Route\Annotation\WSController $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                // 类中间件
                $classMiddlewares = [];
                foreach (AnnotationManager::getClassAnnotations($className, WSMiddleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, WSAction::class) as $methodName => $actionAnnotations)
                {
                    /** @var \Imi\Swoole\Server\WebSocket\Route\Annotation\WSRoute[] $routes */
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, WSRoute::class);
                    if (!isset($routes[0]))
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
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
                        $route->addRuleAnnotation($routeItem, new RouteCallable($server, $className, $methodName), [
                            'middlewares' => $middlewares,
                            'singleton'   => null === $classAnnotation->singleton ? Config::get('@server.' . $name . '.controller.singleton', false) : $classAnnotation->singleton,
                        ]);
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
            if (!$server instanceof \Imi\Swoole\Server\WebSocket\Server)
            {
                continue;
            }
            $context['server'] = $server;
            $route = $server->getBean('WSRoute');
            foreach (Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $routeOption)
            {
                $routeAnnotation = new WSRoute($routeOption['route'] ?? []);
                if (isset($routeOption['callback']))
                {
                    $callable = $routeOption['callback'];
                }
                else
                {
                    $callable = new RouteCallable($server, $routeOption['controller'], $routeOption['method']);
                }
                $route->addRuleAnnotation($routeAnnotation, $callable, [
                    'middlewares' => $routeOption['middlewares'],
                ]);
            }
            unset($context['server']);
        }
    }
}
