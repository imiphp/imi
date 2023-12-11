<?php

declare(strict_types=1);

namespace Imi\Workerman\Server\WebSocket;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\DataParser\JsonObjectParser;
use Imi\Server\Protocol;
use Imi\Server\WebSocket\Contract\IWebSocketServer;
use Imi\Server\WebSocket\Enum\NonControlFrameType;
use Imi\Server\WebSocket\Message\Frame;
use Imi\Util\Http\Consts\StatusCode;
use Imi\Util\ImiPriority;
use Imi\Workerman\Event\WorkermanEvents;
use Imi\Workerman\Http\Message\WorkermanRequest;
use Imi\Workerman\Http\Message\WorkermanResponse;
use Imi\Workerman\Server\Base;
use Imi\Workerman\Server\Http\Event\WorkermanHttpRequestEvent;
use Imi\Workerman\Server\Http\Listener\BeforeRequest;
use Imi\Workerman\Server\WebSocket\Event\WebSocketConnectEvent;
use Imi\Workerman\Server\WebSocket\Event\WorkermanWebSocketMessageEvent;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Websocket;

#[Bean(name: 'WorkermanWebSocketServer')]
class Server extends Base implements IWebSocketServer
{
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
        $this->worker->protocol = Websocket::class;
        $this->nonControlFrameType = $config['nonControlFrameType'] ?? NonControlFrameType::Text;
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
            Event::on(WorkermanEvents::SERVER_HTTP_REQUEST, [new BeforeRequest(), 'handle'], ImiPriority::IMI_MAX);
            App::set('has_imi_workerman_http_request_event', true);
        }
        // @phpstan-ignore-next-line
        $this->worker->onWebSocketConnect = function (TcpConnection $connection, string $httpHeader): void {
            try
            {
                // @phpstan-ignore-next-line
                $connection->websocketType = NonControlFrameType::Text === $this->nonControlFrameType ? Websocket::BINARY_TYPE_BLOB : Websocket::BINARY_TYPE_ARRAYBUFFER;
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
                ConnectionContext::muiltiSet([
                    'uri'        => (string) $request->getUri(),
                    'dataParser' => $this->config['dataParser'] ?? JsonObjectParser::class,
                ]);
                Event::dispatch(new WorkermanHttpRequestEvent($this, $request, $response));
                Event::dispatch(new WebSocketConnectEvent($this, $clientId, $request, $response, $connection));
                if (!\in_array($response->getStatusCode(), [StatusCode::OK, StatusCode::SWITCHING_PROTOCOLS]))
                {
                    $connection->close();
                }
            }
            catch (\Throwable $th)
            {
                $connection->close();
                Log::error($th);
            }
            finally
            {
                RequestContext::destroy();
            }
        };

        $this->worker->onMessage = function (TcpConnection $connection, string $data): void {
            try
            {
                $clientId = $connection->id;
                RequestContext::muiltiSet([
                    'server'       => $this,
                    'clientId'     => $clientId,
                ]);
                Event::dispatch(new WorkermanWebSocketMessageEvent($this, $clientId, $data, new Frame($data, $clientId), $connection));
            }
            catch (\Throwable $th)
            {
                // @phpstan-ignore-next-line
                if (true !== $this->getBean('WebSocketErrorHandler')->handle($th))
                {
                    Log::error($th);
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
    public function push(int|string $clientId, string $data, int $opcode = 1): bool
    {
        /** @var TcpConnection|null $connection */
        $connection = $this->worker->connections[$clientId] ?? null;
        if (!$connection)
        {
            throw new \RuntimeException(sprintf('Connection %s does not exists', $clientId));
        }

        return false !== $connection->send($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getNonControlFrameType(): NonControlFrameType
    {
        return $this->nonControlFrameType;
    }
}
