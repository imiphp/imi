<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer\Listener;

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
use Imi\Swoole\Server\UdpServer\Parser\UdpControllerParser;
use Imi\Swoole\Server\UdpServer\Route\Annotation\UdpAction;
use Imi\Swoole\Server\UdpServer\Route\Annotation\UdpMiddleware;
use Imi\Swoole\Server\UdpServer\Route\Annotation\UdpRoute;
use Imi\Swoole\Worker;

/**
 * UDP 服务器路由初始化.
 *
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class UdpRouteInit implements IEventListener
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
        $controllerParser = UdpControllerParser::getInstance();
        $context = RequestContext::getContext();
        foreach (ServerManage::getServers() as $name => $server)
        {
            if (!$server instanceof \Imi\Swoole\Server\UdpServer\Server)
            {
                continue;
            }
            $context['server'] = $server;
            /** @var \Imi\Swoole\Server\UdpServer\Route\UdpRoute $route */
            $route = $server->getBean('UdpRoute');
            foreach ($controllerParser->getByServer($name) as $className => $classItem)
            {
                /** @var \Imi\Swoole\Server\UdpServer\Route\Annotation\UdpController $classAnnotation */
                $classAnnotation = $classItem->getAnnotation();
                // 类中间件
                $classMiddlewares = [];
                foreach (AnnotationManager::getClassAnnotations($className, UdpMiddleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                }
                foreach (AnnotationManager::getMethodsAnnotations($className, UdpAction::class) as $methodName => $methodItem)
                {
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, UdpRoute::class);
                    if (!isset($routes[0]))
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    foreach (AnnotationManager::getMethodAnnotations($className, $methodName, UdpMiddleware::class) ?? [] as $middleware)
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $this->getMiddlewares($middleware->middlewares, $name));
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));

                    foreach ($routes as $routeItem)
                    {
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
            if (!$server instanceof \Imi\Swoole\Server\UdpServer\Server)
            {
                continue;
            }
            $context['server'] = $server;
            $route = $server->getBean('UdpRoute');
            foreach (Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $routeOption)
            {
                $routeAnnotation = new UdpRoute($routeOption['route'] ?? []);
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
