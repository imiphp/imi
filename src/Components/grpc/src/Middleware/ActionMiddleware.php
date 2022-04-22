<?php

declare(strict_types=1);

namespace Imi\Grpc\Middleware;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\BeanFactory;
use Imi\Bean\ReflectionUtil;
use Imi\Controller\HttpController;
use Imi\Grpc\Parser;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Route\RouteResult;
use Imi\Server\View\View;
use Imi\Util\DelayServerBeanCallable;
use Imi\Util\Http\Consts\MediaType;
use Imi\Util\Http\Consts\RequestHeader;
use Imi\Util\Http\Consts\ResponseHeader;
use Imi\Util\Http\Response;
use Imi\Util\Stream\MemoryStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @Bean(name="GrpcActionMiddleware", recursion=false)
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
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 获取Response对象
        $response = $handler->handle($request);
        // 获取路由结果
        $context = RequestContext::getContext();
        $context['response'] = $response;
        /** @var \Imi\Server\Http\Route\RouteResult|null $result */
        $result = $context['routeResult'];
        if (null === $result)
        {
            throw new \RuntimeException('RequestContent not found routeResult');
        }
        // 路由匹配结果是否是[控制器对象, 方法名]
        $isObject = \is_array($result->callable) && isset($result->callable[0]) && $result->callable[0] instanceof HttpController;
        $useObjectRequestAndResponse = $isObject;
        if ($useObjectRequestAndResponse)
        {
            // 复制一份控制器对象
            $result->callable[0] = clone $result->callable[0];
            // 传入Request和Response对象
            $result->callable[0]->request = $request;
            $result->callable[0]->response = $response;
        }
        // 执行动作
        // @phpstan-ignore-next-line
        $actionResult = ($result->callable)(...$this->prepareActionParams($request, $result));
        // 视图
        $finalResponse = null;
        if ($actionResult instanceof Response)
        {
            $finalResponse = $actionResult;
        }
        else
        {
            if ($useObjectRequestAndResponse)
            {
                // 获得控制器中的Response
                $finalResponse = $result->callable[0]->response;
            }
            else
            {
                $finalResponse = $context['response'];
            }
            if ($actionResult instanceof \Google\Protobuf\Internal\Message)
            {
                $finalResponse = $finalResponse->withBody(new MemoryStream(Parser::serializeMessage($actionResult)));
            }
            else
            {
                if ($actionResult instanceof \Imi\Server\View\Annotation\View)
                {
                    // 动作返回的值是@View注解
                    $viewAnnotation = $actionResult;
                }
                else
                {
                    // 获取对应动作的视图注解
                    $viewAnnotation = clone $result->routeItem->view;
                    if (null !== $viewAnnotation)
                    {
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
                }
                // 视图渲染
                $options = $viewAnnotation->toArray();
                $finalResponse = $this->view->render($viewAnnotation, $viewAnnotation->data, $options, $finalResponse);
            }
        }

        /** @var \Imi\Server\Http\Message\Response $finalResponse */
        if (!$finalResponse->hasHeader(ResponseHeader::CONTENT_TYPE))
        {
            $finalResponse->setHeader(ResponseHeader::CONTENT_TYPE, MediaType::GRPC_PROTO);
        }
        if (!$finalResponse->hasHeader(RequestHeader::TRAILER))
        {
            $finalResponse->setHeader(RequestHeader::TRAILER, 'grpc-status, grpc-message');
        }
        if (!$finalResponse->hasTrailer('grpc-status'))
        {
            $finalResponse->setTrailer('grpc-status', '0');
        }
        if (!$finalResponse->hasTrailer('grpc-message'))
        {
            $finalResponse->setTrailer('grpc-message', '');
        }

        return $finalResponse;
    }

    /**
     * 准备调用action的参数.
     */
    private function prepareActionParams(Request $request, RouteResult $routeResult): array
    {
        $callable = $routeResult->callable;
        // 根据动作回调类型获取反射
        if ($callable instanceof DelayServerBeanCallable)
        {
            $class = $callable->getBeanName();
            $method = $callable->getMethodName();
            if (isset($this->actionMethodParams[$class][$method]))
            {
                $params = $this->actionMethodParams[$class][$method];
            }
            else
            {
                $ref = new \ReflectionMethod($class, $method);
                $params = $this->actionMethodParams[$class][$method] = $ref->getParameters();
            }
        }
        elseif (\is_array($callable))
        {
            if (\is_string($callable[0]))
            {
                $class = $callable[0];
            }
            else
            {
                $class = BeanFactory::getObjectClass($callable[0]);
            }
            $method = $callable[1];
            if (isset($this->actionMethodParams[$class][$method]))
            {
                $params = $this->actionMethodParams[$class][$method];
            }
            else
            {
                $ref = new \ReflectionMethod($class, $method);
                $params = $this->actionMethodParams[$class][$method] = $ref->getParameters();
            }
        }
        elseif ($callable instanceof \Closure || \is_string($callable))
        {
            $ref = new \ReflectionFunction($callable);
            $params = $ref->getParameters();
        }
        else
        {
            return [];
        }
        /** @var \ReflectionParameter[] $params */
        $param = $params[0] ?? null;
        if (!$param)
        {
            return [];
        }
        if ($type = $param->getType())
        {
            $type = ReflectionUtil::getTypeCode($type);
            if (is_subclass_of($type, \Google\Protobuf\Internal\Message::class))
            {
                $value = Parser::deserializeMessage([$type, null], (string) $request->getBody());
                if (null === $value)
                {
                    throw new \RuntimeException(sprintf('RequestData %s deserialize failed', $type));
                }

                return [$value];
            }
        }

        return [];
    }
}
