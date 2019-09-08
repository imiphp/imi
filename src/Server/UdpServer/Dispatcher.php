<?php
namespace Imi\Server\UdpServer;

use Imi\App;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\DataParser\DataParser;
use Imi\Server\UdpServer\Message\IPacketData;

/**
 * @Bean("UdpDispatcher")
 */
class Dispatcher
{
    /**
     * 中间件数组
     * @var string[]
     */
    protected $middlewares = [];

    public function dispatch(IPacketData $data)
    {
        $requestHandler = new PacketHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($data);
        if(null !== $responseData)
        {
            $clientInfo = $data->getClientInfo();
            RequestContext::getServer()->getSwooleServer()->sendTo($clientInfo['address'], $clientInfo['port'], RequestContext::getServerBean(DataParser::class)->encode($responseData));
        }
    }

    protected function getMiddlewares()
    {
        return array_merge($this->middlewares, [
            \Imi\Server\UdpServer\Middleware\ActionWrapMiddleware::class,
        ]);
    }
}