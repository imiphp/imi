<?php
namespace Imi\Server\WebSocket;

use Imi\App;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\WebSocket\Message\IFrame;
use Imi\Server\DataParser\DataParser;

/**
 * @Bean("WebSocketDispatcher")
 */
class Dispatcher
{
    /**
     * 中间件数组
     * @var string[]
     */
    protected $middlewares = [];

    /**
     * 最终使用的中间件列表
     *
     * @var array
     */
    private $finalMiddlewares;

    public function dispatch(IFrame $frame)
    {
        $requestHandler = new MessageHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($frame);
        if(null !== $responseData)
        {
            RequestContext::getServer()->getSwooleServer()->push($frame->getFd(), RequestContext::getServerBean(DataParser::class)->encode($responseData));
        }
    }

    /**
     * 获取中间件列表
     *
     * @return array
     */
    protected function getMiddlewares(): array
    {
        $finalMiddlewares = &$this->finalMiddlewares;
        if(null === $finalMiddlewares)
        {
            return $finalMiddlewares = array_merge($this->middlewares, [
                \Imi\Server\WebSocket\Middleware\ActionWrapMiddleware::class,
            ]);
        }
        return $finalMiddlewares;
    }

}