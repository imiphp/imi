<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\UdpServer;

use Imi\App;
use Imi\Bean\Annotation\Bean;
use Imi\Server\Protocol;
use Imi\Server\ServerManager;
use Imi\Swoole\Server\Base;
use Imi\Swoole\Server\Contract\ISwooleServer;
use Imi\Swoole\Server\Contract\ISwooleUdpServer;
use Imi\Swoole\Server\Event\Param\PacketEventParam;

/**
 * UDP 服务器类.
 *
 * @Bean(name="UdpServer", env="swoole")
 */
class Server extends Base implements ISwooleUdpServer
{
    /**
     * 是否支持 SSL.
     */
    private bool $ssl = false;

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::UDP;
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
            'sockType'  => isset($this->config['sockType']) ? (\SWOOLE_SOCK_UDP | $this->config['sockType']) : \SWOOLE_SOCK_UDP,
            'mode'      => $this->config['mode'] ?? \SWOOLE_BASE,
        ];
    }

    /**
     * 绑定服务器事件.
     */
    protected function __bindEvents(): void
    {
        if ($event = ($this->config['events']['packet'] ?? true))
        {
            $this->swoolePort->on('packet', \is_callable($event) ? $event : function (\Swoole\Server $server, string $data, array $clientInfo) {
                try
                {
                    $this->trigger('packet', [
                        'server'        => $this,
                        'data'          => $data,
                        'clientInfo'    => $clientInfo,
                    ], $this, PacketEventParam::class);
                }
                catch (\Throwable $ex)
                {
                    App::getBean('ErrorLog')->onException($ex);
                }
            });
        }
        else
        {
            $this->swoolePort->on('packet', function () {
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
        return false;
    }

    /**
     * 向客户端发送消息.
     */
    public function sendTo(string $ip, int $port, string $data): bool
    {
        return $this->getSwooleServer()->sendto($ip, $port, $data);
    }
}
