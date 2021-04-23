<?php

declare(strict_types=1);

namespace Imi\Server\Http\Middleware;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\ReflectionContainer;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Http\Annotation\ExtractData;
use Imi\Server\Http\Controller\HttpController;
use Imi\Server\Http\Controller\SingletonHttpController;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Http\Route\RouteResult;
use Imi\Server\Http\Struct\ActionMethodItem;
use Imi\Server\Session\Session;
use Imi\Server\View\View;
use Imi\Util\ObjectArrayHelper;
use InvalidArgumentException;
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
     * ExtractData 集合注解缓存.
     *
     * @var ExtractData[][]
     */
    private array $extractDataAnnotationCaches = [];

    /**
     * 处理方法.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 获取Response对象
        $response = $handler->handle($request);
        // 获取路由结果
        $context = RequestContext::getContext();
        $context['response'] = $response;
        if (null === ($result = $context['routeResult']))
        {
            throw new \RuntimeException('RequestContent not found routeResult');
        }
        /** @var \Imi\Server\Http\Route\RouteResult $result */
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
        // @phpstan-ignore-next-line
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
     */
    private function prepareActionParams(Request $request, RouteResult $routeResult): array
    {
        if (isset($this->actionMethodCaches[$routeResult->id]))
        {
            $actionMethodCache = $this->actionMethodCaches[$routeResult->id];
            $extractDataAnnotationCache = $this->extractDataAnnotationCaches[$routeResult->id];
        }
        else
        {
            $callable = $routeResult->callable;
            $extractDataAnnotationCache = [];
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

                /** @var ExtractData $extractData */
                foreach (AnnotationManager::getMethodAnnotations($class, $method, ExtractData::class) as $extractData)
                {
                    $extractDataAnnotationCache[$extractData->to] = $extractData;
                }
            }
            elseif (!$callable instanceof \Closure)
            {
                $ref = new \ReflectionFunction($callable);
                $params = $ref->getParameters();
            }
            if (!isset($params) || !$params)
            {
                return $this->actionMethodCaches[$routeResult->id] = $this->extractDataAnnotationCaches[$routeResult->id] = [];
            }
            $actionMethodCache = [];
            /** @var \ReflectionParameter[] $params */
            foreach ($params as $param)
            {
                $hasDefault = $param->isDefaultValueAvailable();
                $actionMethodCache[] = new ActionMethodItem(
                    $param->name,
                    $hasDefault,
                    $hasDefault ? $param->getDefaultValue() : null,
                    $param->allowsNull(),
                    $param->getType()
                );
            }
            $this->actionMethodCaches[$routeResult->id] = $actionMethodCache;
            $this->extractDataAnnotationCaches[$routeResult->id] = $extractDataAnnotationCache;
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

        if ($extractDataAnnotationCache)
        {
            $allData = [
                '$get'      => $get,
                '$post'     => $post,
                '$body'     => $parsedBody,
                '$headers'  => [],
                '$cookie'   => $request->getCookieParams(),
                '$session'  => Session::get(),
                '$this'     => $request,
            ];
            $headers = &$allData['$headers'];
            foreach ($request->getHeaders() as $name => $values)
            {
                $headers[$name] = implode(', ', $values);
            }
        }

        /** @var ActionMethodItem[] $actionMethodCache */
        foreach ($actionMethodCache as $actionMethodCacheItem)
        {
            $paramName = $actionMethodCacheItem->getName();
            if (isset($extractDataAnnotationCache[$paramName]))
            {
                /** @var ExtractData $extractData */
                $extractData = $extractDataAnnotationCache[$paramName];
                $value = ObjectArrayHelper::get($allData, $extractData->name, $extractData->default);
            }
            elseif (isset($routeResult->params[$paramName]))
            {
                // 路由解析出来的参数
                $value = $routeResult->params[$paramName];
            }
            elseif (isset($post[$paramName]))
            {
                // post
                $value = $post[$paramName];
            }
            elseif (isset($get[$paramName]))
            {
                // get
                $value = $get[$paramName];
            }
            elseif ($parsedBodyIsObject && isset($parsedBody->{$paramName}))
            {
                $value = $parsedBody->{$paramName};
            }
            elseif ($parsedBodyIsArray && isset($parsedBody[$paramName]))
            {
                $value = $parsedBody[$paramName];
            }
            elseif ($actionMethodCacheItem->hasDefault())
            {
                $value = $actionMethodCacheItem->getDefault();
            }
            elseif ($actionMethodCacheItem->allowNull())
            {
                $value = null;
            }
            else
            {
                throw new InvalidArgumentException(sprintf('Missing parameter: %s', $paramName));
            }
            switch ($actionMethodCacheItem->getType())
            {
                case 'int':
                    $value = (int) $value;
                    break;
                case 'float':
                    $value = (float) $value;
                    break;
                case 'bool':
                    $value = (bool) $value;
                    break;
            }
            $result[] = $value;
        }

        return $result;
    }
}
