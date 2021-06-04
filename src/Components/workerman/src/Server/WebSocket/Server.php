<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\DataParser\JsonObjectParser;
use Imi\Server\Protocol;
use Imi\Server\WebSocket\Contract\IWebSocketServer;
use Imi\Server\WebSocket\Message\Frame;
use Imi\Workerman\Http\Message\WorkermanRequest;
use Imi\Workerman\Server\Base;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Websocket;

/**
 * @Bean("WorkermanWebSocketServer")
 */
class Server extends Base implements IWebSocketServer
{
    /**
     * 构造方法.
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker->protocol = Websocket::class;
    }

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::WEBSOCKET;
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return true;
    }

    /**
     * 绑定服务器事件.
     */
    protected function bindEvents(): void
    {
        parent::bindEvents();

        // @phpstan-ignore-next-line
        $this->worker->onWebSocketConnect = function (TcpConnection $connection, string $httpHeader): void {
            $clientId = $connection->id;
            $request = new WorkermanRequest($this->worker, $connection, new Request($httpHeader), 'ws');

            RequestContext::muiltiSet([
                'server'       => $this,
                'clientId'     => $clientId,
            ]);
            ConnectionContext::create([
                'uri'        => (string) $request->getUri(),
                'dataParser' => $this->config['dataParser'] ?? JsonObjectParser::class,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT', [
                'server'           => $this,
                'connection'       => $connection,
                'clientId'         => $clientId,
                'request'          => $request,
            ], $this);
        };

        $this->worker->onMessage = function (TcpConnection $connection, string $data) {
            $clientId = $connection->id;
            RequestContext::muiltiSet([
                'server'       => $this,
                'clientId'     => $clientId,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE', [
                'server'           => $this,
                'connection'       => $connection,
                'clientId'         => $clientId,
                'data'             => $data,
                'frame'            => new Frame($data, $clientId),
            ], $this);
        };
    }

    /**
     * 获取实例化 Worker 用的协议.
     */
    protected function getWorkerScheme(): string
    {
        return 'websocket';
    }

    /**
     * 向客户端推送消息.
     *
     * @param int|string $clientId
     */
    public function push($clientId, string $data, int $opcode = 1): bool
    {
        /** @var TcpConnection|null $connection */
        $connection = $this->worker->connections[$clientId] ?? null;
        if (!$connection)
        {
            throw new \RuntimeException(sprintf('Connection %s does not exists', $clientId));
        }

        return false !== $connection->send($data);
    }
}
