<?php

declare(strict_types=1);

namespace Imi\WorkermanGateway\Workerman\Server\Business;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Lib\Gateway;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\DataParser\JsonObjectParser;
use Imi\Server\Protocol;
use Imi\Server\Server;
use Imi\Server\WebSocket\Message\Frame;
use Imi\Util\Socket\IPEndPoint;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Server\Event\ConnectEvent;
use Imi\Workerman\Server\Http\Event\WorkermanConnectionCloseEvent;
use Imi\Workerman\Server\WebSocket\Event\WebSocketConnectEvent;
use Imi\Workerman\Server\WebSocket\Event\WorkermanWebSocketMessageEvent;
use Imi\WorkermanGateway\Workerman\Http\Message\WorkermanRequest;

#[Bean(name: 'WorkermanGatewayWebSocketBusinessServer')]
class WebSocketBusinessServer extends \Imi\Workerman\Server\WebSocket\Server
{
    /**
     * {@inheritDoc}
     */
    protected string $workerClass = BusinessWorker::class;

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        Event::one(WorkermanEvents::SERVER_WORKER_START, function (): void {
            $this->bindBusinessEvents();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::WEBSOCKET;
    }

    protected function bindBusinessEvents(): void
    {
        $worker = $this->worker;
        $refClass = new \ReflectionClass($worker);

        $property = $refClass->getProperty('_eventOnConnect');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId): void {
            RequestContext::muiltiSet([
                'server'   => $this,
                'clientId' => $clientId,
            ]);
            ConnectionContext::muiltiSet([
                '__clientAddress' => $_SERVER['REMOTE_ADDR'],
                '__clientPort'    => $_SERVER['REMOTE_PORT'],
            ]);
            Event::dispatch(new ConnectEvent($this, $clientId));
            RequestContext::destroy();
        });

        $property = $refClass->getProperty('_eventOnWebSocketConnect');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId, array $data): void {
            try
            {
                $request = new WorkermanRequest($this->worker, $clientId, $data);

                RequestContext::muiltiSet([
                    'server'       => $this,
                    'clientId'     => $clientId,
                ]);
                ConnectionContext::muiltiSet([
                    'uri'             => (string) $request->getUri(),
                    'dataParser'      => $this->config['dataParser'] ?? JsonObjectParser::class,
                ]);
                Event::dispatch(new WebSocketConnectEvent($this, $clientId, $request));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                Gateway::closeClient($clientId);
                throw $th;
            }
        });

        $property = $refClass->getProperty('_eventOnClose');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId): void {
            RequestContext::muiltiSet([
                'server'   => $this,
                'clientId' => $clientId,
            ]);
            Event::dispatch(new WorkermanConnectionCloseEvent($this, $clientId));
            RequestContext::destroy();
        });

        $property = $refClass->getProperty('_eventOnMessage');
        $property->setAccessible(true);
        $property->setValue($worker, function (string $clientId, $data): void {
            try
            {
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                ]);

                Event::dispatch(new WorkermanWebSocketMessageEvent($this, $clientId, $data, new Frame($data, $clientId)));
                RequestContext::destroy();
            }
            catch (\Throwable $th)
            {
                // @phpstan-ignore-next-line
                if (true !== $this->getBean('WebSocketErrorHandler')->handle($th))
                {
                    Log::error($th);
                }
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function push(int|string $clientId, string $data, int $opcode = 1): bool
    {
        return Server::sendRaw($data, $clientId, $this->getName()) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress(string|int $clientId): IPEndPoint
    {
        $session = Gateway::getSession($clientId);

        return new IPEndPoint($session['__clientAddress'], $session['__clientPort']);
    }
}
