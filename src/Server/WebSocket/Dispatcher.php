<?php

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
     * @var string[]|\Imi\Server\WebSocket\Middleware\IMiddleware[]
     */
    protected $middlewares = [];

    /**
     * 最终使用的中间件列表.
     *
     * @var \Imi\Server\WebSocket\Middleware\IMiddleware[]|null
     */
    private $finalMiddlewares;

    /**
     * @param \Imi\Server\WebSocket\Message\IFrame $frame
     *
     * @return void
     */
    public function dispatch(IFrame $frame)
    {
        $requestHandler = new MessageHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($frame);
        if (null !== $responseData)
        {
            // @phpstan-ignore-next-line
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
        $finalMiddlewares = &$this->finalMiddlewares;
        if (null === $finalMiddlewares)
        {
            return $finalMiddlewares = array_merge($this->middlewares, [
                \Imi\Server\WebSocket\Middleware\ActionWrapMiddleware::class,
            ]);
        }

        return $finalMiddlewares;
    }
}
