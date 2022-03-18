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
use Imi\Worker;

/**
 * TCP 服务器类.
 *
 * @Bean(name="TcpServer", env="swoole")
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
     * {@inheritDoc}
     */
    public function __construct(string $name, array $config, bool $isSubServer = false)
    {
        parent::__construct($name, $config, $isSubServer);
        $this->syncConnect = $config['syncConnect'] ?? true;
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::TCP;
    }

    /**
     * {@inheritDoc}
     */
    protected function createServer(): void
    {
        $config = $this->getServerInitConfig();
        $this->swooleServer = new \Swoole\Server($config['host'], $config['port'], $config['mode'], $config['sockType']);
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
        $configs = &$this->config['configs'];
        foreach (static::SWOOLE_PROTOCOLS as $protocol)
        {
            $configs[$protocol] ??= false;
        }
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    protected function __bindEvents(): void
    {
        $events = $this->config['events'] ?? null;
        if ($event = ($events['connect'] ?? true))
        {
            $this->swoolePort->on('connect', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId) {
                try
                {
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
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
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
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
            $this->swoolePort->on('connect', static function () {
            });
        }

        if ($event = ($events['receive'] ?? true))
        {
            $this->swoolePort->on('receive', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId, string $data) {
                try
                {
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
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
                    // @phpstan-ignore-next-line
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        else
        {
            $this->swoolePort->on('receive', static function () {
            });
        }

        if ($event = ($events['close'] ?? true))
        {
            $this->swoolePort->on('close', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId) {
                try
                {
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
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
            $this->swoolePort->on('close', static function () {
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isSSL(): bool
    {
        return $this->ssl;
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
    public function send($clientId, string $data): bool
    {
        return $this->getSwooleServer()->send((int) $clientId, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function isSyncConnect(): bool
    {
        return $this->syncConnect;
    }
}
