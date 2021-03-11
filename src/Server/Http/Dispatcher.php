<?php

namespace Imi\Server\Http;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Response;

/**
 * @Bean("HttpDispatcher")
 */
class Dispatcher
{
    /**
     * 中间件数组.
     *
     * @var string[]|\Psr\Http\Server\MiddlewareInterface[]
     */
    protected $middlewares = [];

    /**
     * 最终使用的中间件列表.
     *
     * @var \Psr\Http\Server\MiddlewareInterface[]|null
     */
    private $finalMiddlewares;

    /**
     * 调度.
     *
     * @param \Imi\Server\Http\Message\Request $request
     *
     * @return \Imi\Server\Http\Message\Response
     */
    public function dispatch($request): Response
    {
        $requestHandler = new RequestHandler($this->getMiddlewares());
        /** @var \Imi\Server\Http\Message\Response $response */
        $response = $requestHandler->handle($request);
        if (!$response->isEnded())
        {
            $response->send();
        }

        return $response;
    }

    /**
     * 获取中间件列表.
     *
     * @return array
     */
    protected function getMiddlewares(): array
    {
        $finalMiddlewares = &$this->finalMiddlewares;
        if (null === $finalMiddlewares)
        {
            return $finalMiddlewares = array_merge($this->middlewares, [
                \Imi\Server\Http\Middleware\ActionWrapMiddleware::class,
            ]);
        }

        return $finalMiddlewares;
    }
}
