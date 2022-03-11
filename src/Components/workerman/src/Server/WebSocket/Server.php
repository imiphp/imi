<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\DataParser\JsonObjectParser;
use Imi\Server\Protocol;
use Imi\Server\WebSocket\Contract\IWebSocketServer;
use Imi\Server\WebSocket\Message\Frame;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\ImiPriority;
use Imi\Workerman\Http\Message\WorkermanRequest;
use Imi\Workerman\Http\Message\WorkermanResponse;
use Imi\Workerman\Server\Base;
use Imi\Workerman\Server\Http\Listener\BeforeRequest;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Websocket;

/**
 * @Bean("WorkermanWebSocketServer")
 */
class Server extends Base implements IWebSocketServer
{
    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config)
    {
        parent::__construct($name, $config);
        $this->worker->protocol = Websocket::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::WEBSOCKET;
    }

    /**
     * {@inheritDoc}
     */
    public function isLongConnection(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function bindEvents(): void
    {
        parent::bindEvents();

        if (!App::get('has_imi_workerman_http_request_event', false))
        {
            Event::on('IMI.WORKERMAN.SERVER.HTTP.REQUEST', [new BeforeRequest(), 'handle'], ImiPriority::IMI_MAX);
            App::set('has_imi_workerman_http_request_event', true);
        }
        // @phpstan-ignore-next-line
        $this->worker->onWebSocketConnect = function (TcpConnection $connection, string $httpHeader): void {
            try
            {
                $clientId = $connection->id;
                $worker = $this->worker;
                $request = new WorkermanRequest($worker, $connection, new Request($httpHeader), 'ws');
                $response = new WorkermanResponse($worker, $connection);
                RequestContext::muiltiSet([
                    'server'   => $this,
                    'clientId' => $clientId,
                    'request'  => $request,
                    'response' => $response,
                ]);
                ConnectionContext::create([
                    'uri'        => (string) $request->getUri(),
                    'dataParser' => $this->config['dataParser'] ?? JsonObjectParser::class,
                ]);
                Event::trigger('IMI.WORKERMAN.SERVER.HTTP.REQUEST', [
                    'server'   => $this,
                    'request'  => $request,
                    'response' => $response,
                ], $this);
                Event::trigger('IMI.WORKERMAN.SERVER.WEBSOCKET.CONNECT', [
                    'server'     => $this,
                    'connection' => $connection,
                    'clientId'   => $clientId,
                    'request'    => $request,
                    'response'   => $response,
                ], $this);
                if (!\in_array($response->getStatusCode(), [StatusCode::OK, StatusCode::SWITCHING_PROTOCOLS]))
                {
                    $connection->close();
                }
            }
            catch (\Throwable $th)
            {
                $connection->close();
                // @phpstan-ignore-next-line
                App::getBean('ErrorLog')->onException($th);
            }
            finally
            {
                RequestContext::destroy();
            }
        };

        $this->worker->onMessage = function (TcpConnection $connection, string $data) {
            try
            {
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
            }
            catch (\Throwable $th)
            {
                // @phpstan-ignore-next-line
                if (true !== $this->getBean('WebSocketErrorHandler')->handle($th))
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($th);
                }
            }
        };
    }

    /**
     * {@inheritDoc}
     */
    protected function getWorkerScheme(): string
    {
        return 'websocket';
    }

    /**
     * {@inheritDoc}
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
