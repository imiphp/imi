<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Business;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\DataParser\JsonObjectParser;
use Imi\Server\Protocol;
use Imi\Server\Server;
use Imi\Server\WebSocket\Message\Frame;
use Imi\Util\Socket\IPEndPoint;
use Imi\WorkermanGateway\Workerman\Http\Message\WorkermanRequest;
use ReflectionClass;
use Workerman\Worker;

/**
 * @Bean("WorkermanGatewayWebSocketBusinessServer")
 */
class WebSocketBusinessServer extends \Imi\Workerman\Server\WebSocket\Server
{
    /**
     * Workerman Worker 类名.
     */
    protected string $workerClass = BusinessWorker::class;

    /**
     * 绑定服务器事件.
     */
    protected function bindEvents(): void
    {
        parent::bindEvents();
        // @phpstan-ignore-next-line
        $this->worker->onWebSocketConnect = null;
        // @phpstan-ignore-next-line
        $this->worker->onMessage = null;
        // @phpstan-ignore-next-line
        $this->worker->onClose = null;
        // @phpstan-ignore-next-line
        $this->worker->onConnect = null;
    }

    /**
     * 构造方法.
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        Event::on('IMI.WORKERMAN.SERVER.WORKER_START', function () {
            $this->bindBusinessEvents();
        });
    }

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::WEBSOCKET;
    }

    protected function bindBusinessEvents(): void
    {
        $worker = $this->worker;
        $refClass = new ReflectionClass($worker);

        $property = $refClass->getProperty('_eventOnConnect');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId) {
            RequestContext::muiltiSet([
                'server'   => $this,
                'clientId' => $clientId,
            ]);
            ConnectionContext::muiltiSet([
                '__clientAddress' => $_SERVER['REMOTE_ADDR'],
                '__clientPort'    => $_SERVER['REMOTE_PORT'],
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.CONNECT', [
                'server'   => $this,
                'clientId' => $clientId,
            ], $this);
        });

        $property = $refClass->getProperty('_eventOnWebSocketConnect');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId, array $data) {
            $request = new WorkermanRequest($this->worker, $clientId, $data);

            RequestContext::muiltiSet([
                'server'       => $this,
                'clientId'     => $clientId,
            ]);
            ConnectionContext::muiltiSet([
                'uri'             => (string) $request->getUri(),
                'dataParser'      => $this->config['dataParser'] ?? JsonObjectParser::class,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT', [
                'server'   => $this,
                'clientId' => $clientId,
                'request'  => $request,
            ], $this);
        });

        $property = $refClass->getProperty('_eventOnClose');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId) {
            RequestContext::muiltiSet([
                'server'   => $this,
                'clientId' => $clientId,
            ]);
            Event::trigger('IMI.WORKERMAN.SERVER.CLOSE', [
                'server'   => $this,
                'clientId' => $clientId,
            ], $this);
        });

        $property = $refClass->getProperty('_eventOnMessage');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId, $data) {
            RequestContext::muiltiSet([
                'server'   => $this,
                'clientId' => $clientId,
            ]);

            Event::trigger('IMI.WORKERMAN.SERVER.WEBSOCKET.MESSAGE', [
                'server'   => $this,
                'clientId' => $clientId,
                'data'     => $data,
                'frame'    => new Frame($data, $clientId),
            ], $this);
        });
    }

    /**
     * 向客户端推送消息.
     *
     * @param int|string $clientId
     */
    public function push($clientId, string $data, int $opcode = 1): bool
    {
        return Server::sendRaw($data, $clientId, $this->getName()) > 0;
    }

    /**
     * 获取客户端地址
     *
     * @param string|int $clientId
     */
    public function getClientAddress($clientId): IPEndPoint
    {
        $session = Gateway::getSession($clientId);

        return new IPEndPoint($session['__clientAddress'], $session['__clientPort']);
    }
}
