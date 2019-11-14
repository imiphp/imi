<?php
namespace Imi\Server\Http\Middleware;

use Imi\RequestContext;
use Imi\Util\Http\Response;
use Imi\Bean\Annotation\Bean;
use Imi\Controller\HttpController;
use Imi\Server\Http\Message\Request;
use Imi\Server\Annotation\ServerInject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
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
        $result = $context['routeResult'];
        if(null === $result)
        {
            throw new \RuntimeException('RequestContent not found routeResult');
        }
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = is_array($result->callable) && isset($result->callable[0]) && $result->callable[0] instanceof HttpController;
        $useObjectRequestAndResponse = $isObject && !$result->routeItem->singleton;
        if($useObjectRequestAndResponse)
        {
            // 复制一份控制器对象
            $result->callable[0] = clone $result->callable[0];
            // 传入Request和Response对象
            $result->callable[0]->request = $request;
            $result->callable[0]->response = $response;
        }
        // 执行动作
        $actionResult = ($result->callable)(...$this->prepareActionParams($request, $result));
        $response = $context['response'];
        if($useObjectRequestAndResponse)
        {
            // 获得控制器中的Response
            $response = $result->callable[0]->response;
        }
        // 视图
        if($actionResult instanceof \Imi\Server\View\Annotation\View)
        {
            // 动作返回的值是@View注解
            $viewAnnotation = $actionResult;
        }
        else if($actionResult instanceof Response)
        {
            $response = $actionResult;
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
            // 视图渲染
            $options = $viewAnnotation->toArray();
            $response = $this->view->render($viewAnnotation->renderType, $viewAnnotation->data, $options, $response);
        }

        return $response;
    }
    
    /**
     * 准备调用action的参数
     * @param Request $request
     * @param array $routeResult
     * @return array
     */
    private function prepareActionParams(Request $request, $routeResult)
    {
        // 根据动作回调类型获取反射
        if(is_array($routeResult->callable))
        {
            if(is_string($routeResult->callable[0]))
            {
                $class = $routeResult->callable[0];
            }
            else
            {
                $class = get_class($routeResult->callable[0]);
            }
            $method = $routeResult->callable[1];
            if(isset($this->actionMethodParams[$class][$method]))
            {
                $params = $this->actionMethodParams[$class][$method];
            }
            else
            {
                $ref = new \ReflectionMethod($routeResult->callable[0], $routeResult->callable[1]);
                $params = $this->actionMethodParams[$class][$method] = $ref->getParameters();
            }
        }
        else if(!$routeResult->callable instanceof \Closure)
        {
            $ref = new \ReflectionFunction($routeResult->callable);
            $params = $ref->getParameters();
        }
        else
        {
            return [];
        }
        $result = [];
        foreach($params as $param)
        {
            if(isset($routeResult->params[$param->name]))
            {
                // 路由解析出来的参数
                $result[] = $routeResult->params[$param->name];
            }
            else if($request->hasPost($param->name))
            {
                // post
                $result[] = $request->post($param->name);
            }
            else if(null !== ($value = $request->get($param->name)))
            {
                // get
                $result[] = $value;
            }
            else
            {
                $parsedBody = $request->getParsedBody();
                if(is_object($parsedBody) && isset($parsedBody->{$param->name}))
                {
                    $result[] = $parsedBody->{$param->name};
                }
                else if(is_array($parsedBody) && isset($parsedBody[$param->name]))
                {
                    $result[] = $parsedBody[$param->name];
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