<?php
namespace Imi\Server\Route\Listener;

use Imi\Worker;
use Imi\Main\Helper;
use Imi\ServerManage;
use Swoole\Coroutine;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;
use Imi\Server\Route\RouteCallable;
use Imi\Server\Route\Annotation\Route;
use Imi\Server\Route\Parser\ControllerParser;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Server\Route\Annotation\Middleware;
use Imi\Server\Route\Annotation\Action;
use Imi\Server\Route\Annotation\WebSocket\WSConfig;
use Imi\RequestContext;
use Imi\Server\Route\TMiddleware;

/**
 * http服务器路由初始化
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 */
class HttpRouteInit implements IEventListener
{
    use TMiddleware;

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
        $controllerParser = ControllerParser::getInstance();
        foreach(ServerManage::getServers() as $name => $server)
        {
            if(!$server instanceof \Imi\Server\Http\Server && !$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            RequestContext::create([
                'server'    =>  $server,
            ]);
            $route = $server->getBean('HttpRoute');
            foreach($controllerParser->getByServer($name) as $className => $classItem)
            {
                $classAnnotation = $classItem->getAnnotation();
                // 类中间件
                $classMiddlewares = [];
                foreach(AnnotationManager::getClassAnnotations($className, Middleware::class) ?? [] as $middleware)
                {
                    $classMiddlewares = array_merge($classMiddlewares, $this->getMiddlewares($middleware->middlewares));
                }
                foreach(AnnotationManager::getMethodsAnnotations($className, Action::class) as $methodName => $actionAnnotations)
                {
                    $routeAnnotations = AnnotationManager::getMethodAnnotations($className, $methodName, Route::class);
                    if(isset($routeAnnotations[0]))
                    {
                        $routes = $routeAnnotations;
                    }
                    else
                    {
                        $routes = [
                            new Route([
                                'url' => $methodName,
                            ])
                        ];
                    }
                    // 方法中间件
                    $methodMiddlewares = [];
                    foreach(AnnotationManager::getMethodAnnotations($className, $methodName, Middleware::class) ?? [] as $middleware)
                    {
                        $methodMiddlewares = array_merge($methodMiddlewares, $this->getMiddlewares($middleware->middlewares));
                    }
                    // 最终中间件
                    $middlewares = array_values(array_unique(array_merge($classMiddlewares, $methodMiddlewares)));
                    
                    foreach($routes as $routeItem)
                    {
                        if(null === $routeItem->url)
                        {
                            $routeItem->url = $methodName;
                        }
                        if((!isset($routeItem->url[0]) || '/' !== $routeItem->url[0]) && '' != $classAnnotation->prefix)
                        {
                            $routeItem->url = $classAnnotation->prefix . $routeItem->url;
                        }
                        $route->addRuleAnnotation($routeItem, new RouteCallable($className, $methodName), [
                            'middlewares'   => $middlewares,
                            'wsConfig'      => AnnotationManager::getMethodAnnotations($className, $methodName, WSConfig::class)[0] ?? null,
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
            if(!$server instanceof \Imi\Server\Http\Server && !$server instanceof \Imi\Server\WebSocket\Server)
            {
                continue;
            }
            $route = $server->getBean('HttpRoute');
            foreach(Helper::getMain($server->getConfig()['namespace'])->getConfig()['route'] ?? [] as $routeOption)
            {
                $routeAnnotation = new Route($routeOption['route'] ?? []);
                if(isset($routeOption['callback']))
                {
                    $callable = $routeOption['callback'];
                }
                else
                {
                    $callable = new RouteCallable($routeOption['controller'], $routeOption['method']);
                }
                $route->addRuleAnnotation($routeAnnotation, $callable, [
                    'middlewares' => $routeOption['middlewares'] ?? [],
                ]);
            }
        }
    }
}