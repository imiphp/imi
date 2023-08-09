<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\TcpServer;

use Imi\Bean\Annotation\Bean;
use Imi\Log\Log;
use Imi\Server\Protocol;
use Imi\Swoole\Server\Base;
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
        $this->swooleServer = new \Swoole\Server($config['host'], (int) $config['port'], (int) $config['mode'], (int) $config['sockType']);
    }

    /**
     * {@inheritDoc}
     */
    protected function createSubServer(): void
    {
        parent::createSubServer();
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
        $host = $this->config['host'] ?? '0.0.0.0';
        $sockType = \Imi\Swoole\Util\Swoole::getTcpSockTypeByHost($host);

        return [
            'host'      => $host,
            'port'      => (int) ($this->config['port'] ?? 8080),
            'sockType'  => isset($this->config['sockType']) ? ($sockType | $this->config['sockType']) : $sockType,
            'mode'      => (int) ($this->config['mode'] ?? \SWOOLE_BASE),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function __bindEvents(): void
    {
        $enableSyncConnect = $this->syncConnect && \SWOOLE_BASE === $this->swooleServer->mode;
        $events = $this->config['events'] ?? null;
        if ($event = ($events['connect'] ?? true))
        {
            $this->swoolePort->on('connect', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId) use ($enableSyncConnect) {
                try
                {
                    if ($enableSyncConnect)
                    {
                        $channelId = 'connection:' . $fd;
                        $channel = ChannelContainer::getChannel($channelId);
                    }
                    if (!Worker::isInited())
                    {
                        ChannelContainer::pop('workerInit');
                    }
                    $this->trigger('connect', [
                        'server'          => $this,
                        'clientId'        => $fd,
                        'reactorId'       => $reactorId,
                    ], $this, ConnectEventParam::class);
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
            $this->swoolePort->on('connect', static function () {
            });
        }

        if ($event = ($events['receive'] ?? true))
        {
            $this->swoolePort->on('receive', \is_callable($event) ? $event : function (\Swoole\Server $server, int $fd, int $reactorId, string $data) use ($enableSyncConnect) {
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
                    $this->trigger('receive', [
                        'server'          => $this,
                        'clientId'        => $fd,
                        'reactorId'       => $reactorId,
                        'data'            => $data,
                    ], $this, ReceiveEventParam::class);
                }
                catch (\Throwable $th)
                {
                    // @phpstan-ignore-next-line
                    if (true !== $this->getBean('TcpErrorHandler')->handle($th))
                    {
                        Log::error($th);
                    }
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
                catch (\Throwable $th)
                {
                    Log::error($th);
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
