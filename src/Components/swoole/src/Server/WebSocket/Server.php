<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket;

use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\Log\Log;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Server\WebSocket\Enum\NonControlFrameType;
use Imi\Swoole\Event\SwooleEvents;
use Imi\Swoole\Http\Message\SwooleRequest;
use Imi\Swoole\Http\Message\SwooleResponse;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleWebSocketServer;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\DisconnectEventParam;
use Imi\Swoole\Server\Event\Param\HandShakeEventParam;
use Imi\Swoole\Server\Event\Param\MessageEventParam;
use Imi\Swoole\Server\Event\Param\RequestEventParam;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Server\Http\Listener\BeforeRequest;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Util\Bit;
use Imi\Util\ImiPriority;
use Imi\Worker;

/**
 * WebSocket 服务器类.
 */
#[Bean(name: 'WebSocketServer', env: 'swoole')]
class Server extends Base implements ISwooleWebSocketServer
{
    /**
     * 是否为 wss 服务
     */
    private bool $wss = false;

    /**
     * 是否为 https 服务
     */
    private bool $https = false;

    /**
     * 是否为 http2 服务
     */
    private bool $http2 = false;

    /**
     * 同步连接，当连接事件执行完后，才执行 receive 事件.
     */
    private bool $syncConnect = true;

    /**
     * 非控制帧类型.
     */
    private NonControlFrameType $nonControlFrameType = NonControlFrameType::Text;

    /**
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config, bool $isSubServer = false)
    {
        parent::__construct($name, $config, $isSubServer);
        $this->syncConnect = $config['syncConnect'] ?? true;
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
    protected function createServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\WebSocket\Server($config['host'], (int) $config['port'], (int) $config['mode'], (int) $config['sockType']);
        $this->https = $this->wss = \defined('SWOOLE_SSL') && Bit::has((int) $config['sockType'], \SWOOLE_SSL);
        $this->http2 = $this->config['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * {@inheritDoc}
     */
    protected function createSubServer(): void
    {
        parent::createSubServer();
        $thisConfig = &$this->config;
        $thisConfig['configs']['open_websocket_protocol'] ??= true;
        $this->wss = \defined('SWOOLE_SSL') && isset($thisConfig['sockType']) && Bit::has($thisConfig['sockType'], \SWOOLE_SSL);
    }

    /**
     * {@inheritDoc}
     */
    protected function getServerInitConfig(): array
    {
        return [
            'host'      => $host = $this->config['host'] ?? '0.0.0.0',
            'port'      => (int) ($this->config['port'] ?? 8080),
            'sockType'  => (int) ($this->config['sockType'] ?? \Imi\Swoole\Util\Swoole::getTcpSockTypeByHost($host)),
            'mode'      => (int) ($this->config['mode'] ?? \SWOOLE_BASE),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function __bindEvents(): void
    {
        Event::one(SwooleEvents::WORKER_APP_START, function (WorkerStartEventParam $e): void {
            // 内置事件监听
            $this->on('request', [new BeforeRequest($this), 'handle'], ImiPriority::IMI_MAX);
        });

        $enableSyncConnect = $this->syncConnect && \SWOOLE_BASE === $this->swooleServer->mode;
        $events = $this->config['events'] ?? null;
        if ($event = ($events['handshake'] ?? true))
        {
            $this->swoolePort->on('handshake', \is_callable($event) ? $event : function (\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse) use ($enableSyncConnect): void {
                try
                {
                    if ($enableSyncConnect)
                    {
                        $channelId = 'connection:' . $swooleRequest->fd;
                        $channel = ChannelContainer::getChannel($channelId);
                    }
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
                    $request = new SwooleRequest($this, $swooleRequest);
                    $response = new SwooleResponse($this, $swooleResponse);
                    RequestContext::create([
                        'server'         => $this,
                        'swooleRequest'  => $swooleRequest,
                        'swooleResponse' => $swooleResponse,
                        'request'        => $request,
                        'response'       => $response,
                        'clientId'       => $swooleRequest->fd,
                    ]);
                    ConnectionContext::create([
                        'uri' => (string) $request->getUri(),
                    ]);
                    $this->dispatch(new HandShakeEventParam($this, $request, $response));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
                finally
                {
                    if (isset($channel, $channelId))
                    {
                        ChannelContainer::removeChannel($channelId);
                    }
                }
            });
        }
        else
        {
            $this->swoolePort->on('handshake', static function (): void {
            });
        }

        if ($event = ($events['message'] ?? true))
        {
            $this->swoolePort->on('message', \is_callable($event) ? $event : function (\Swoole\Server $server, \Swoole\WebSocket\Frame $frame) use ($enableSyncConnect): void {
                try
                {
                    if ($enableSyncConnect)
                    {
                        $channelId = 'connection:' . $frame->fd;
                        if (ChannelContainer::hasChannel($channelId))
                        {
                            ChannelContainer::pop($channelId);
                        }
                    }
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
                    RequestContext::muiltiSet([
                        'server'        => $this,
                        'clientId'      => $frame->fd,
                    ]);
                    $this->dispatch(new MessageEventParam($this, $frame));
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
        else
        {
            $this->swoolePort->on('message', static function (): void {
            });
        }

        if ($event = ($events['close'] ?? true))
        {
            $this->swoolePort->on('close', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId) use ($enableSyncConnect): void {
                try
                {
                    if ($enableSyncConnect)
                    {
                        $channelId = 'connection:' . $fd;
                        if (ChannelContainer::hasChannel($channelId))
                        {
                            ChannelContainer::pop($channelId);
                        }
                    }
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
                    RequestContext::muiltiSet([
                        'server'        => $this,
                    ]);
                    $this->dispatch(new CloseEventParam($this, $fd, $reactorId));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            });
        }
        else
        {
            $this->swoolePort->on('close', static function (): void {
            });
        }

        if ($event = ($events['request'] ?? true))
        {
            $this->swoolePort->on('request', \is_callable($event) ? $event : function (\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse): void {
                try
                {
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
                    $request = new SwooleRequest($this, $swooleRequest);
                    $response = new SwooleResponse($this, $swooleResponse);
                    RequestContext::muiltiSet([
                        'server'         => $this,
                        'swooleRequest'  => $swooleRequest,
                        'swooleResponse' => $swooleResponse,
                        'request'        => $request,
                        'response'       => $response,
                    ]);
                    $this->dispatch(new RequestEventParam($this, $request, $response));
                }
                catch (\Throwable $th)
                {
                    // @phpstan-ignore-next-line
                    if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                    {
                        Log::error($th);
                    }
                }
            });
        }
        else
        {
            $this->swoolePort->on('request', static function (): void {
            });
        }

        if ($event = ($events['disconnect'] ?? true))
        {
            $this->swoolePort->on('disconnect', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd): void {
                try
                {
                    RequestContext::muiltiSet([
                        'server'        => $this,
                    ]);
                    $this->dispatch(new DisconnectEventParam($this, $fd));
                }
                catch (\Throwable $th)
                {
                    Log::error($th);
                }
            });
        }
        else
        {
            $this->swoolePort->on('disconnect', static function (): void {
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isSSL(): bool
    {
        return $this->wss;
    }

    /**
     * {@inheritDoc}
     */
    public function isHttps(): bool
    {
        return $this->https;
    }

    /**
     * {@inheritDoc}
     */
    public function isHttp2(): bool
    {
        return $this->http2;
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
    public function push(int|string $clientId, string $data, int $opcode = 1): bool
    {
        // @phpstan-ignore-next-line
        return $this->getSwooleServer()->push($clientId, $data, $opcode);
    }

    /**
     * {@inheritDoc}
     */
    public function getNonControlFrameType(): NonControlFrameType
    {
        return $this->nonControlFrameType;
    }
}
