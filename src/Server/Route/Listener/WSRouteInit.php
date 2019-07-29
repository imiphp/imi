<?php
namespace Imi\Server\Route\Listener;

use Imi\Main\Helper;
use Imi\ServerManage;
use Imi\RequestContext;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Route\RouteCallable;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Server\Route\Parser\WSControllerParser;
use Imi\Server\Route\Annotation\WebSocket\WSRoute;
use Imi\Server\Route\Annotation\WebSocket\WSAction;
use Imi\Server\Route\Annotation\WebSocket\WSMiddleware;

/**
 * WebSocket 服务器路由初始化
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class WSRouteInit implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        $this->parseAnnotations($e);
        $this->parseConfigs();
    }

    /**
     * 处理注解路由
     * @return void
     */
    private function parseAnnotations(EventParam $e)
    {
        $controllerParser = WSControllerParser::getInstance();
        foreach(ServerManage::getServers() as $name => $server)
        {
            if(!$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            RequestContext::create();
            RequestContext::set('server', $server);
            $route = $server->getBean('WSRoute');
            foreach($controllerParser->getByServer($name) as $className => $classItem)
            {
                $classAnnotation = $classItem->getAnnotation();
                // 类中间件
                $classMiddlewares = [];
                foreach(AnnotationManager::getClassAnnotations($className, WSMiddleware::class) ?? [] as $middleware)
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
                foreach(AnnotationManager::getMethodsAnnotations($className, WSAction::class) as $methodName => $actionAnnotations)
                {
                    $routes = AnnotationManager::getMethodAnnotations($className, $methodName, WSRoute::class);
                    if(!isset($routes[0]))
                    {
                        throw new \RuntimeException(sprintf('%s->%s method has no route', $className, $methodName));
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    foreach(AnnotationManager::getMethodAnnotations($className, $methodName, WSMiddleware::class) ?? [] as $middleware)
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
    private function parseConfigs()
    {
        foreach(ServerManage::getServers() as $server)
        {
            if(!$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            $route = $server->getBean('WSRoute');
            foreach(Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $routeOption)
            {
                $routeAnnotation = new WSRoute($routeOption['route'] ?? []);
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