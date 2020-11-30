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
     *
     * @var array
     */
    private array $finalMiddlewares;

    /**
     * 调度.
     *
     * @param \Imi\Server\Http\Message\Request $request
     *
     * @return \Imi\Server\Http\Message\Response
     */
    public function dispatch(Request $request): Response
    {
        $requestHandler = new RequestHandler($this->getMiddlewares());
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
        if (!isset($this->finalMiddlewares))
        {
            return $this->finalMiddlewares = array_merge($this->middlewares, [
                \Imi\Server\Http\Middleware\ActionWrapMiddleware::class,
            ]);
        }

        return $this->finalMiddlewares;
    }
}
