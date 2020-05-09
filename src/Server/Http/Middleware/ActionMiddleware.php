<?php
namespace Imi\Server\Http\Middleware;

use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Controller\HttpController;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Annotation\ServerInject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Imi\Controller\SingletonHttpController;
use Psr\Http\Message\ServerRequestInterface;
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
    protected $view;

    /**
     * @ServerInject("HttpRequestProxy")
     *
     * @var \Imi\Server\Http\Message\Proxy\RequestProxy
     */
    protected $requestProxy;

    /**
     * @ServerInject("HttpResponseProxy")
     *
     * @var \Imi\Server\Http\Message\Proxy\ResponseProxy
     */
    protected $responseProxy;

    /**
     * 动作方法参数缓存
     *
     * @var \ReflectionParameter[]
     */
    private $actionMethodParams = [];

    /**
     * 处理方法
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
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
        if(null === ($result = $context['routeResult']))
        {
            throw new \RuntimeException('RequestContent not found routeResult');
        }
        $callable = &$result->callable;
        if($isSingleton = $result->routeItem->singleton)
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
        if($isObject)
        {
            if($isSingletonController = ($object instanceof SingletonHttpController))
            {
                // 传入Request和Response代理对象
                $object->request = $this->requestProxy;
                $object->response = $this->responseProxy;
            }
            else
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
        if($actionResult instanceof Response)
        {
            $finalResponse = $actionResult;
        }
        else if($actionResult instanceof \Imi\Server\View\Annotation\View)
        {
            // 动作返回的值是@View注解
            $viewAnnotation = $actionResult;
        }
        else
        {
            // 获取对应动作的视图注解
            $viewAnnotation = clone $result->routeItem->view;
            if(null !== $viewAnnotation)
            {
                if([] !== $viewAnnotation->data && is_array($actionResult))
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
        }

        if(isset($viewAnnotation))
        {
            if(!$finalResponse)
            {
                if($isObject && !$isSingletonController && !$isSingleton)
                {
                    // 获得控制器中的Response
                    $finalResponse = $object->response;
                }
                else
                {
                    $finalResponse = $context['response'];
                }
            }
            // 视图渲染
            $options = $viewAnnotation->toArray();
            $finalResponse = $this->view->render($viewAnnotation->renderType, $viewAnnotation->data, $options, $finalResponse);
        }

        return $finalResponse;
    }
    
    /**
     * 准备调用action的参数
     * @param Request $request
     * @param array $routeResult
     * @return array
     */
    private function prepareActionParams(Request $request, $routeResult)
    {
        $callable = $routeResult->callable;
        // 根据动作回调类型获取反射
        if(is_array($callable))
        {
            if(is_string($callable[0]))
            {
                $class = $callable[0];
            }
            else
            {
                $class = get_class($callable[0]);
            }
            $method = $callable[1];
            $actionMethodParams = &$this->actionMethodParams;
            if(isset($actionMethodParams[$class][$method]))
            {
                $params = $actionMethodParams[$class][$method];
            }
            else
            {
                $ref = new \ReflectionMethod($class, $method);
                $params = $actionMethodParams[$class][$method] = $ref->getParameters();
            }
        }
        else if(!$callable instanceof \Closure)
        {
            $ref = new \ReflectionFunction($callable);
            $params = $ref->getParameters();
        }
        else
        {
            return [];
        }
        $result = [];
        foreach($params as $param)
        {
            $paramName = $param->name;
            if(isset($routeResult->params[$paramName]))
            {
                // 路由解析出来的参数
                $result[] = $routeResult->params[$paramName];
            }
            else if($request->hasPost($paramName))
            {
                // post
                $result[] = $request->post($paramName);
            }
            else if(null !== ($value = $request->get($paramName)))
            {
                // get
                $result[] = $value;
            }
            else
            {
                $parsedBody = $request->getParsedBody();
                if(is_object($parsedBody) && isset($parsedBody->{$paramName}))
                {
                    $result[] = $parsedBody->{$paramName};
                }
                else if(is_array($parsedBody) && isset($parsedBody[$paramName]))
                {
                    $result[] = $parsedBody[$paramName];
                }
                else if($param->isOptional())
                {
                    // 方法默认值
                    $result[] = $param->getDefaultValue();
                }
                else
                {
                    $result[] = null;
                }
            }
        }
        return $result;
    }
    
}