<?php
namespace Imi\Server\Route\Listener;

use Imi\Main\Helper;
use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\Annotation\Udp\UdpRoute;
use Imi\Server\Route\Annotation\Udp\UdpAction;
use Imi\Server\Route\Parser\UdpControllerParser;
use Imi\Server\Route\Annotation\Udp\UdpMiddleware;

/**
 * UDP 服务器路由初始化
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class UdpRouteInit implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $this->parseAnnotations($e);
        $this->parseConfigs($e);
    }

    /**
     * 处理注解路由
     * @return void
     */
    private function parseAnnotations(EventParam $e)
    {
        $controllerParser = UdpControllerParser::getInstance();
        foreach(ServerManage::getServers() as $name => $server)
        {
            if(!$server instanceof \Imi\Server\UdpServer\Server)
            {
                continue;
            }
            RequestContext::create();
            RequestContext::set('server', $server);
            $route = $server->getBean('UdpRoute');
            foreach($controllerParser->getByServer($name) as $className => $classItem)
            {
                $classAnnotation = $classItem['annotation'];
                // 类中间件
                $classMiddlewares = [];
                foreach(AnnotationManager::getClassAnnotations($className, UdpMiddleware::class) ?? [] as $middleware)
                {
                    if(is_array($middleware->middlewares))
                    {
                        $classMiddlewares = array_merge($classMiddlewares, $middleware->middlewares);
                    }
                    else
                    {
                        $classMiddlewares[] = $middleware->middlewares;
                    }
                }
                foreach(AnnotationManager::getMethodsAnnotations($className, UdpAction::class) as $methodName => $methodItem)
                {
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, UdpRoute::class);
                    if(!isset($routes[0]))
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    foreach(AnnotationManager::getMethodAnnotations($className, $methodName, UdpMiddleware::class) ?? [] as $middleware)
                    {
                        if(is_array($middleware->middlewares))
                        {
                            $methodMiddlewares = array_merge($methodMiddlewares, $middleware->middlewares);
                        }
                        else
                        {
                            $methodMiddlewares[] = $middleware->middlewares;
                        }
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));
                    
                    foreach($routes as $routeItem)
                    {
                        $route->addRuleAnnotation($routeItem, new RouteCallable($className, $methodName), [
                            'middlewares' => $middlewares,
                        ]);
                    }
                }
            }
            RequestContext::destroy();
        }
    }

    /**
     * 处理配置文件路由
     * @return void
     */
    private function parseConfigs(EventParam $e)
    {
        foreach(ServerManage::getServers() as $name => $server)
        {
            if(!$server instanceof \Imi\Server\UdpServer\Server)
            {
                continue;
            }
            $route = $server->getBean('UdpRoute');
            foreach(Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $url => $routeOption)
            {
                $routeAnnotation = new UdpRoute($routeOption['route'] ?? []);
                if(isset($routeOption['callback']))
                {
                    $callable = $routeOption['callback'];
                }
                else
                {
                    $callable = new RouteCallable($routeOption['controller'], $routeOption['method']);
                }
                $route->addRuleAnnotation($routeAnnotation, $callable, [
                    'middlewares' => $routeOption['middlewares'],
                ]);
            }
        }
    }
}