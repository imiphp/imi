<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\WebSocket\Message\IFrame;

/**
 * @Bean("WebSocketDispatcher")
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

    public function dispatch(IFrame $frame)
    {
        $requestHandler = new MessageHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($frame);
        if (null !== $responseData)
        {
            RequestContext::getServer()->getSwooleServer()->push($frame->getFd(), RequestContext::getServerBean(DataParser::class)->encode($responseData));
        }
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
                \Imi\Server\WebSocket\Middleware\ActionWrapMiddleware::class,
            ]);
        }

        return $this->finalMiddlewares;
    }
}
