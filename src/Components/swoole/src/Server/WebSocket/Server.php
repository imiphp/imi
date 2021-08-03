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
use Imi\Server\WebSocket\Contract\IWebSocketServer;
use Imi\Swoole\Http\Message\SwooleRequest;
use Imi\Swoole\Http\Message\SwooleResponse;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\HandShakeEventParam;
use Imi\Swoole\Server\Event\Param\MessageEventParam;
use Imi\Swoole\Server\Event\Param\RequestEventParam;
use Imi\Swoole\Server\Event\Param\WorkerStartEventParam;
use Imi\Swoole\Server\Http\Listener\BeforeRequest;
use Imi\Swoole\Util\Co\ChannelContainer;
use Imi\Util\Bit;
use Imi\Util\ImiPriority;
use Swoole\WebSocket\Server as WebSocketServer;

/**
 * WebSocket 服务器类.
 *
 * @Bean("WebSocketServer")
 */
class Server extends Base implements IWebSocketServer
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
     * 同步连接，当连接事件执行完后，才执行 message 事件.
     */
    private bool $syncConnect = true;

    /**
     * 构造方法.
     *
     * @param bool $isSubServer 是否为子服务器
     */
    public function __construct(string $name, array $config, bool $isSubServer = false)
    {
        parent::__construct($name, $config, $isSubServer);
        $this->syncConnect = $config['syncConnect'] ?? true;
    }

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::WEBSOCKET;
    }

    /**
     * 创建 swoole 服务器对象
     */
    protected function createServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\WebSocket\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
        $this->https = $this->wss = \defined('SWOOLE_SSL') && Bit::has($config['sockType'], \SWOOLE_SSL);
        $this->http2 = $this->config['configs']['open_http2_protocol'] ?? false;
    }

    /**
     * 从主服务器监听端口，作为子服务器.
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
     * 获取服务器初始化需要的配置.
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
     * 绑定服务器事件.
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
                    if ($this->syncConnect)
                    {
                        $channelId = 'connection:' . $swooleRequest->fd;
                        $channel = ChannelContainer::getChannel($channelId);
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
                    $this->trigger('handShake', [
                        'request'   => $request,
                        'response'  => $response,
                    ], $this, HandShakeEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
                finally
                {
                    var_dump(isset($channel));
                    if (isset($channel))
                    {
                        while (($channel->stats()['consumer_num'] ?? 0) > 0)
                        {
                            var_dump('push');
                            $channel->push(1);
                        }
                        ChannelContainer::removeChannel($channelId);
                    }
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
                    if ($this->syncConnect)
                    {
                        $channelId = 'connection:' . $frame->fd;
                        if (ChannelContainer::hasChannel($channelId))
                        {
                            ChannelContainer::pop($channelId);
                        }
                    }
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
                    if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                    {
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
     * 是否为 wss 服务
     */
    public function isSSL(): bool
    {
        return $this->wss;
    }

    /**
     * 是否为 https 服务
     */
    public function isHttps(): bool
    {
        return $this->https;
    }

    /**
     * 是否为 http2 服务
     */
    public function isHttp2(): bool
    {
        return $this->http2;
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return true;
    }

    /**
     * 向客户端推送消息.
     *
     * @param int|string $clientId
     */
    public function push($clientId, string $data, int $opcode = 1): bool
    {
        // @phpstan-ignore-next-line
        return $this->getSwooleServer()->push($clientId, $data, $opcode);
    }
}
