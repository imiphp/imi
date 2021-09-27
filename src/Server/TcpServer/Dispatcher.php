<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\TcpServer\Contract\ITcpServer;
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
     */
    private array $finalMiddlewares = [];

    public function dispatch(IReceiveData $data): void
    {
        $requestHandler = new ReceiveHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($data);
        if (null !== $responseData)
        {
            /** @var ITcpServer $server */
            $server = RequestContext::getServer();
            $server->send($data->getClientId(), $server->getBean(DataParser::class)->encode($responseData));
        }
    }

    /**
     * 获取中间件列表.
     */
    protected function getMiddlewares(): array
    {
        if (!$this->finalMiddlewares)
        {
            $middlewares = $this->middlewares;
            $middlewares[] = \Imi\Server\TcpServer\Middleware\ActionWrapMiddleware::class;

            return $this->finalMiddlewares = $middlewares;
        }

        return $this->finalMiddlewares;
    }
}
