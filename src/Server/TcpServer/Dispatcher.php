<?php

namespace Imi\Server\TcpServer;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * @Bean("TcpDispatcher")
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

    public function dispatch(IReceiveData $data)
    {
        $requestHandler = new ReceiveHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($data);
        if (null !== $responseData)
        {
            RequestContext::getServer()->getSwooleServer()->send($data->getFd(), RequestContext::getServerBean(DataParser::class)->encode($responseData));
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
                \Imi\Server\TcpServer\Middleware\ActionWrapMiddleware::class,
            ]);
        }

        return $this->finalMiddlewares;
    }
}
