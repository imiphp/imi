<?php

namespace Imi\Server\Http\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\ReflectionContainer;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Controller\SingletonHttpController;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Http\Route\RouteResult;
use Imi\Server\Http\Struct\ActionMethodItem;
use Imi\Server\View\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean("ActionMiddleware")
 */
class ActionMiddleware implements MiddlewareInterface
{
    /**
     * @ServerInject("View")
     *
     * @var \Imi\Server\View\View
     */
    protected View $view;

    /**
     * 动作方法参数缓存.
     *
     * @var \ReflectionParameter[]
     */
    private array $actionMethodParams = [];

    /**
     * 动作方法缓存.
     *
     * @var ActionMethodItem[][]
     */
    private array $actionMethodCaches = [];

    /**
     * 处理方法.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 获取Response对象
        $response = $handler->handle($request);
        // 获取路由结果
        $context = RequestContext::getContext();
        $context['response'] = $response;
        /** @var \Imi\Server\Http\Route\RouteResult $result */
        if (null === ($result = $context['routeResult']))
        {
            throw new \RuntimeException('RequestContent not found routeResult');
        }
        $callable = &$result->callable;
        $routeItem = $result->routeItem;
        if ($isSingleton = $routeItem->singleton)
        {
            $object = $callable[0];
        }
        else
        {
            // 复制一份控制器对象
            $object = $callable[0] = clone $callable[0];
        }
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = $callable[0] instanceof HttpController;
        if ($isObject)
        {
            if (!($isSingletonController = ($object instanceof SingletonHttpController)))
            {
                // 传入Request和Response对象
                $object->request = $request;
                $object->response = $response;
            }
        }
        // 执行动作
        $actionResult = ($callable)(...$this->prepareActionParams($request, $result));
        // 视图
        $finalResponse = null;
        if ($actionResult instanceof Response)
        {
            $finalResponse = $actionResult;
        }
        elseif ($actionResult instanceof \Imi\Server\View\Annotation\View)
        {
            // 动作返回的值是@View注解
            $viewAnnotation = $actionResult;
        }
        else
        {
            // 获取对应动作的视图注解
            $viewAnnotation = clone $routeItem->view;
            if ([] !== $viewAnnotation->data && \is_array($actionResult))
            {
                // 动作返回值是数组，合并到视图注解
                $viewAnnotation->data = array_merge($viewAnnotation->data, $actionResult);
            }
            else
            {
                // 非数组直接赋值
                $viewAnnotation->data = $actionResult;
            }
        }

        if (isset($viewAnnotation))
        {
            if ($isObject && !$isSingletonController && !$isSingleton)
            {
                // 获得控制器中的Response
                $finalResponse = $object->response;
            }
            else
            {
                $finalResponse = $context['response'];
            }
            // 视图渲染
            $finalResponse = $this->view->render($viewAnnotation->renderType, $viewAnnotation->data, [
                'baseDir'     => $viewAnnotation->baseDir,
                'template'    => $viewAnnotation->template,
                'renderType'  => $viewAnnotation->renderType,
                'data'        => $viewAnnotation->data,
            ], $finalResponse);
        }

        return $finalResponse;
    }

    /**
     * 准备调用action的参数.
     *
     * @param Request                            $request
     * @param \Imi\Server\Http\Route\RouteResult $routeResult
     *
     * @return array
     */
    private function prepareActionParams(Request $request, RouteResult $routeResult): array
    {
        if (isset($this->actionMethodCaches[$routeResult->id]))
        {
            $actionMethodCache = $this->actionMethodCaches[$routeResult->id];
        }
        else
        {
            $callable = $routeResult->callable;
            // 根据动作回调类型获取反射
            if (\is_array($callable))
            {
                if (\is_string($callable[0]))
                {
                    $class = $callable[0];
                }
                else
                {
                    $class = \get_class($callable[0]);
                }
                $method = $callable[1];
                $actionMethodParams = &$this->actionMethodParams;
                if (isset($actionMethodParams[$class][$method]))
                {
                    $params = $actionMethodParams[$class][$method];
                }
                else
                {
                    $ref = ReflectionContainer::getMethodReflection($class, $method);
                    $params = $actionMethodParams[$class][$method] = $ref->getParameters();
                }
            }
            elseif (!$callable instanceof \Closure)
            {
                $ref = new \ReflectionFunction($callable);
                $params = $ref->getParameters();
            }
            else
            {
                $this->actionMethodCaches[$routeResult->id] = [];

                return [];
            }
            if (!$params)
            {
                $this->actionMethodCaches[$routeResult->id] = [];

                return [];
            }
            $actionMethodCache = [];
            /** @var \ReflectionParameter[] $params */
            foreach ($params as $param)
            {
                $actionMethodCache[] = new ActionMethodItem(
                    $param->name,
                    $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                );
            }
            $this->actionMethodCaches[$routeResult->id] = $actionMethodCache;
        }
        if (!$actionMethodCache)
        {
            return [];
        }
        $result = [];
        $get = $request->get();
        $post = $request->post();
        $parsedBody = $request->getParsedBody();
        $parsedBodyIsObject = \is_object($parsedBody);
        if ($parsedBodyIsObject)
        {
            $parsedBodyIsArray = false;
        }
        else
        {
            $parsedBodyIsArray = \is_array($parsedBody);
        }
        /** @var ActionMethodItem[] $actionMethodCache */
        foreach ($actionMethodCache as $actionMethodCacheItem)
        {
            $paramName = $actionMethodCacheItem->getName();
            if (isset($routeResult->params[$paramName]))
            {
                // 路由解析出来的参数
                $result[] = $routeResult->params[$paramName];
            }
            elseif (isset($post[$paramName]))
            {
                // post
                $result[] = $post[$paramName];
            }
            elseif (isset($get[$paramName]))
            {
                // get
                $result[] = $get[$paramName];
            }
            elseif ($parsedBodyIsObject && isset($parsedBody->{$paramName}))
            {
                $result[] = $parsedBody->{$paramName};
            }
            elseif ($parsedBodyIsArray && isset($parsedBody[$paramName]))
            {
                $result[] = $parsedBody[$paramName];
            }
            else
            {
                // 方法默认值
                $result[] = $actionMethodCacheItem->getDefault();
            }
        }

        return $result;
    }
}
