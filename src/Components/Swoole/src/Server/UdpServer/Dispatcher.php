<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext;
use Imi\Swoole\Server\DataParser\DataParser;
use Imi\Swoole\Server\UdpServer\Message\IPacketData;

/**
 * @Bean("UdpDispatcher")
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

    public function dispatch(IPacketData $data)
    {
        $requestHandler = new PacketHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($data);
        if (null !== $responseData)
        {
            $clientInfo = $data->getClientInfo();
            RequestContext::getServer()->getSwooleServer()->sendTo($clientInfo['address'], $clientInfo['port'], RequestContext::getServerBean(DataParser::class)->encode($responseData));
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
                \Imi\Swoole\Server\UdpServer\Middleware\ActionWrapMiddleware::class,
            ]);
        }

        return $this->finalMiddlewares;
    }
}
