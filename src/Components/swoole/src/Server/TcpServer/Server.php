<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Protocol;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Contract\ISwooleTcpServer;
use Imi\Swoole\Server\Event\Param\CloseEventParam;
use Imi\Swoole\Server\Event\Param\ConnectEventParam;
use Imi\Swoole\Server\Event\Param\ReceiveEventParam;
use Imi\Swoole\Util\Co\ChannelContainer;

/**
 * TCP 服务器类.
 *
 * @Bean("TcpServer")
 */
class Server extends Base implements ISwooleTcpServer
{
    /**
     * 是否支持 SSL.
     */
    private bool $ssl = false;

    /**
     * 同步连接，当连接事件执行完后，才执行 receive 事件.
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
        return Protocol::TCP;
    }

    /**
     * 创建 swoole 服务器对象
     */
    protected function createServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
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
        $configs = &$this->config['configs'];
        foreach (static::SWOOLE_PROTOCOLS as $protocol)
        {
            $configs[$protocol] ??= false;
        }
    }

    /**
     * 获取服务器初始化需要的配置.
     */
    protected function getServerInitConfig(): array
    {
        return [
            'host'      => $this->config['host'] ?? '0.0.0.0',
            'port'      => $this->config['port'] ?? 8080,
            'sockType'  => isset($this->config['sockType']) ? (\SWOOLE_SOCK_TCP | $this->config['sockType']) : \SWOOLE_SOCK_TCP,
            'mode'      => $this->config['mode'] ?? \SWOOLE_BASE,
        ];
    }

    /**
     * 绑定服务器事件.
     */
    protected function __bindEvents(): void
    {
        $events = $this->config['events'] ?? null;
        if ($event = ($events['connect'] ?? true))
        {
            $this->swoolePort->on('connect', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId) {
                try
                {
                    if ($this->syncConnect)
                    {
                        $channelId = 'connection:' . $fd;
                        $channel = ChannelContainer::getChannel($channelId);
                    }
                    $this->trigger('connect', [
                        'server'          => $this,
                        'clientId'        => $fd,
                        'reactorId'       => $reactorId,
                    ], $this, ConnectEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
                finally
                {
                    if (isset($channel, $channelId))
                    {
                        while (($channel->stats()['consumer_num'] ?? 0) > 0)
                        {
                            $channel->push(1);
                        }
                        ChannelContainer::removeChannel($channelId);
                    }
                }
            });
        }
        else
        {
            $this->swoolePort->on('connect', function () {
            });
        }

        if ($event = ($events['receive'] ?? true))
        {
            $this->swoolePort->on('receive', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId, string $data) {
                try
                {
                    if ($this->syncConnect)
                    {
                        $channelId = 'connection:' . $fd;
                        if (ChannelContainer::hasChannel($channelId))
                        {
                            ChannelContainer::pop($channelId);
                        }
                    }
                    $this->trigger('receive', [
                        'server'          => $this,
                        'clientId'        => $fd,
                        'reactorId'       => $reactorId,
                        'data'            => $data,
                    ], $this, ReceiveEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        else
        {
            $this->swoolePort->on('receive', function () {
            });
        }

        if ($event = ($events['close'] ?? true))
        {
            $this->swoolePort->on('close', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId) {
                try
                {
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
    }

    /**
     * 是否为 https 服务
     */
    public function isSSL(): bool
    {
        return $this->ssl;
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return true;
    }

    /**
     * 向客户端发送消息.
     *
     * @param int|string $clientId
     */
    public function send($clientId, string $data): bool
    {
        return $this->getSwooleServer()->send((int) $clientId, $data);
    }

    /**
     * 是否同步连接.
     */
    public function isSyncConnect(): bool
    {
        return $this->syncConnect;
    }
}
