<?php
namespace Imi\Server\TcpServer;

use Imi\App;
use Imi\RequestContext;
use Imi\Bean\Annotation\Bean;
use Imi\Server\DataParser\DataParser;
use Imi\Server\TcpServer\Message\IReceiveData;

/**
 * @Bean("TcpDispatcher")
 */
class Dispatcher
{
    /**
     * 中间件数组
     * @var string[]
     */
    protected $middlewares = [];

    public function dispatch(IReceiveData $data)
    {
        $requestHandler = new ReceiveHandler($this->getMiddlewares());
        $responseData = $requestHandler->handle($data);
        if(null !== $responseData)
        {
            RequestContext::getServer()->getSwooleServer()->send($data->getFd(), RequestContext::getServerBean(DataParser::class)->encode($responseData));
        }
    }

    protected function getMiddlewares()
    {
        return array_merge($this->middlewares, [

        ]);
    }
}