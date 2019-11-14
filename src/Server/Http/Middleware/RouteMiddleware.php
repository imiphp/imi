<?php
namespace Imi\Server\Http\Middleware;

use Imi\App;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Util\Stream\MemoryStream;
use Imi\Controller\HttpController;
use Imi\Server\Http\RequestHandler;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;
use Imi\Server\Annotation\ServerInject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Imi\Server\Http\Middleware\ActionMiddleware;

/**
 * @Bean("RouteMiddleware")
 */
class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @ServerInject("HttpRoute")
     *
     * @var \Imi\Server\Http\Route\HttpRoute
     */
    protected $route;

    /**
     * @ServerInject("HttpNotFoundHandler")
     *
     * @var \Imi\Server\Http\Error\IHttpNotFoundHandler
     */
    protected $notFoundHandler;

    /**
     * 处理方法
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RequestContext::getContext();
        // 路由解析
        $result = $this->route->parse($request);
        if(null === $result || !is_callable($result->callable))
        {
            // 未匹配到路由
            $response = $this->notFoundHandler->handle($handler, $request, $context['response']);
        }
        else
        {
            $context['routeResult'] = $result;
            $response = $handler->handle($request);
        }
        return $context['response'] = $response;
    }

}