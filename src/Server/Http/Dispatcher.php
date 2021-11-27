<?php

declare(strict_types=1);

namespace Imi\Server\Http;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\Annotation\ServerInject;
use Imi\Server\Http\Message\Contract\IHttpRequest;
use Imi\Server\Http\Message\Contract\IHttpResponse;
use Imi\Server\Http\Message\Response;
use Imi\Server\Http\Middleware\ActionMiddleware;
use Imi\Server\Http\Middleware\RouteMiddleware;

/**
 * @Bean("HttpDispatcher")
 */
class Dispatcher
{
    /**
     * 中间件数组.
     *
     * @var string[]
     */
    protected array $middlewares = [];

    /**
     * 是否启用中间件.
     */
    protected bool $middleware = true;

    /**
     * @ServerInject("RouteMiddleware")
     */
    protected RouteMiddleware $routeMiddleware;

    /**
     * @ServerInject("ActionMiddleware")
     */
    protected ActionMiddleware $actionMiddleware;

    /**
     * 最终使用的中间件列表.
     */
    private array $finalMiddlewares = [];

    /**
     * 调度.
     */
    public function dispatch(IHttpRequest $request): IHttpResponse
    {
        if ($this->middleware)
        {
            $requestHandler = new RequestHandler($this->getMiddlewares());
            /** @var Response $response */
            $response = $requestHandler->handle($request);
        }
        else
        {
            $context = RequestContext::getContext();
            $response = $context['response'];
            $result = $this->routeMiddleware->dispatch($request, $response);
            if ($result)
            {
                $response = $result;
            }
            elseif ($result = $this->actionMiddleware->dispatch($request, $response))
            {
                $response = $result;
            }
        }
        $response->send();

        return $response;
    }

    /**
     * 获取中间件列表.
     */
    protected function getMiddlewares(): array
    {
        if (!$this->finalMiddlewares)
        {
            $middlewares = $this->middlewares;
            $middlewares[] = \Imi\Server\Http\Middleware\ActionWrapMiddleware::class;

            return $this->finalMiddlewares = $middlewares;
        }

        return $this->finalMiddlewares;
    }
}
