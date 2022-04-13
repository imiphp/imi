<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;
use Imi\Server\UdpServer\Contract\IUdpServer;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean(name="UdpDispatcher", recursion=false)
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

    public function dispatch(IPacketData $data): void
    {
        $requestHandler = new PacketHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($data);
        if (null !== $responseData)
        {
            /** @var IUdpServer $server */
            $server = RequestContext::getServer();
            $address = $data->getClientAddress();
            $server->sendTo($address->getAddress(), $address->getPort(), $server->getBean(DataParser::class)->encode($responseData));
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
            $middlewares[] = \Imi\Server\UdpServer\Middleware\ActionWrapMiddleware::class;

            return $this->finalMiddlewares = $middlewares;
        }

        return $this->finalMiddlewares;
    }
}
