<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\WebSocket;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\ConnectionContext;
use Imi\Event\Event;
use Imi\RequestContext;
use Imi\Server\Protocol;
use Imi\Server\ServerManager;
use Imi\Swoole\Http\Message\SwooleRequest;
use Imi\Swoole\Http\Message\SwooleResponse;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Contract\ISwooleWebSocketServer;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\HandShakeEventParam;
use Imi\Swoole\Server\Event\Param\MessageEventParam;
use Imi\Swoole\Server\Event\Param\RequestEventParam;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Server\Http\Listener\BeforeRequest;
use Imi\Util\Bit;
use Imi\Util\ImiPriority;
use Swoole\WebSocket\Server as WebSocketServer;

/**
 * WebSocket 服务器类.
 *
 * @Bean(name="WebSocketServer", env="swoole")
 */
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
        $this->swooleServer = new \Swoole\WebSocket\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        $this->https = $this->wss = \defined('SWOOLE_SSL') && Bit::has($config['sockType'], \SWOOLE_SSL);
        $this->http2 = $this->config['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * {@inheritDoc}
     */
    protected function createSubServer(): void
    {
        $config = $this->getServerInitConfig();
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);
        $this->swooleServer = $server->getSwooleServer();
        $this->swoolePort = $this->swooleServer->addListener($config['host'], $config['port'], $config['sockType']);
        $thisConfig = &$this->config;
        $thisConfig['configs']['open_websocket_protocol'] ??= true;
        $this->wss = \defined('SWOOLE_SSL') && Bit::has($config['sockType'], \SWOOLE_SSL);
    }

    /**
     * {@inheritDoc}
     */
    protected function getServerInitConfig(): array
    {
        return [
            'host'      => $this->config['host'] ?? '0.0.0.0',
            'port'      => $this->config['port'] ?? 8080,
            'sockType'  => $this->config['sockType'] ?? \SWOOLE_SOCK_TCP,
            'mode'      => $this->config['mode'] ?? \SWOOLE_BASE,
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function __bindEvents(): void
    {
        Event::one('IMI.MAIN_SERVER.WORKER.START.APP', function (WorkerStartEventParam $e) {
            // 内置事件监听
            $this->on('request', [new BeforeRequest($this), 'handle'], ImiPriority::IMI_MAX);
        });

        $events = $this->config['events'] ?? null;
        if ($event = ($events['handshake'] ?? true))
        {
            $this->swoolePort->on('handshake', \is_callable($event) ? $event : function (\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse) {
                try
                {
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
                    $this->trigger('handShake', [
                        'request'   => $request,
                        'response'  => $response,
                    ], $this, HandShakeEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        else
        {
            $this->swoolePort->on('handshake', function () {
            });
        }

        if ($event = ($events['message'] ?? true))
        {
            $this->swoolePort->on('message', \is_callable($event) ? $event : function (WebSocketServer $server, \Swoole\WebSocket\Frame $frame) {
                try
                {
                    RequestContext::muiltiSet([
                        'server'        => $this,
                        'clientId'      => $frame->fd,
                    ]);
                    $this->trigger('message', [
                        'server'    => $this,
                        'frame'     => $frame,
                    ], $this, MessageEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        else
        {
            $this->swoolePort->on('message', function () {
            });
        }

        if ($event = ($events['close'] ?? true))
        {
            $this->swoolePort->on('close', \is_callable($event) ? $event : function (WebSocketServer $server, int $fd, int $reactorId) {
                try
                {
                    RequestContext::muiltiSet([
                        'server'        => $this,
                    ]);
                    $this->trigger('close', [
                        'server'          => $this,
                        'clientId'        => $fd,
                        'reactorId'       => $reactorId,
                    ], $this, CloseEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        else
        {
            $this->swoolePort->on('close', function () {
            });
        }

        if ($event = ($events['request'] ?? true))
        {
            $this->swoolePort->on('request', \is_callable($event) ? $event : function (\Swoole\Http\Request $swooleRequest, \Swoole\Http\Response $swooleResponse) {
                try
                {
                    $request = new SwooleRequest($this, $swooleRequest);
                    $response = new SwooleResponse($this, $swooleResponse);
                    RequestContext::muiltiSet([
                        'server'         => $this,
                        'swooleRequest'  => $swooleRequest,
                        'swooleResponse' => $swooleResponse,
                        'request'        => $request,
                        'response'       => $response,
                    ]);
                    $this->trigger('request', [
                        'request'  => $request,
                        'response' => $response,
                    ], $this, RequestEventParam::class);
                }
                catch (\Throwable $th)
                {
                    // @phpstan-ignore-next-line
                    if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                    {
                        // @phpstan-ignore-next-line
                        App::getBean('ErrorLog')->onException($th);
                    }
                }
            });
        }
        else
        {
            $this->swoolePort->on('request', function () {
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
    public function push($clientId, string $data, int $opcode = 1): bool
    {
        // @phpstan-ignore-next-line
        return $this->getSwooleServer()->push($clientId, $data, $opcode);
    }
}
