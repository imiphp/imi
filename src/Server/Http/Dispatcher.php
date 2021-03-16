<?php

declare(strict_types=1);

namespace Imi\Server\Http;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Http\Message\Request;
use Imi\Server\Http\Message\Response;

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
     * 最终使用的中间件列表.
     */
    private array $finalMiddlewares = [];

    /**
     * 调度.
     */
    public function dispatch(Request $request): Response
    {
        $requestHandler = new RequestHandler($this->getMiddlewares());
        /** @var Response $response */
        $response = $requestHandler->handle($request);
        if (!$response->isEnded())
        {
            $response->send();
        }

        return $response;
    }

    /**
     * 获取中间件列表.
     */
    protected function getMiddlewares(): array
    {
        if (!$this->finalMiddlewares)
        {
            return $this->finalMiddlewares = array_merge($this->middlewares, [
                \Imi\Server\Http\Middleware\ActionWrapMiddleware::class,
            ]);
        }

        return $this->finalMiddlewares;
    }
}
