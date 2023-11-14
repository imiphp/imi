<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Gateway;

use GatewayWorker\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\WebSocket\Enum\NonControlFrameType;
use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Websocket;

#[Bean(name: 'WorkermanGatewayGatewayServer')]
class GatewayServer extends \Imi\Workerman\Server\Tcp\Server
{
    /**
     * {@inheritDoc}
     */
    protected string $workerClass = Gateway::class;

    /**
     * 非控制帧类型.
     */
    private NonControlFrameType $nonControlFrameType = NonControlFrameType::Text;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->nonControlFrameType = $config['nonControlFrameType'] ?? NonControlFrameType::Text;
    }

    /**
     * 绑定服务器事件.
     */
    protected function bindEvents(): void
    {
        parent::bindEvents();
        $this->worker->onConnect = function (ConnectionInterface $connection): void {
            try
            {
                // @phpstan-ignore-next-line
                $connection->websocketType = NonControlFrameType::Text === $this->nonControlFrameType ? Websocket::BINARY_TYPE_BLOB : Websocket::BINARY_TYPE_ARRAYBUFFER;
                // @phpstan-ignore-next-line
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                ]);
                Event::trigger('IMI.WORKERMAN.SERVER.CONNECT', [
                    'server'     => $this,
                    'clientId'   => $clientId,
                    'connection' => $connection,
                ], $this);
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Log::error($th);
            }
        };
    }

    /**
     * Get 非控制帧类型.
     */
    public function getNonControlFrameType(): NonControlFrameType
    {
        return $this->nonControlFrameType;
    }
}
