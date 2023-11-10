<?php

declare(strict_types=1);

namespace Imi\Test\Component\Server;

use Imi\Server\Contract\BaseServer;
use Imi\Util\Socket\IPEndPoint;

class TestServer extends BaseServer
{
    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return 'test';
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return false;
    }

    /**
     * 是否支持 SSL.
     */
    public function isSSL(): bool
    {
        return false;
    }

    /**
     * 开启服务
     */
    public function start(): void
    {
    }

    /**
     * 终止服务
     */
    public function shutdown(): void
    {
    }

    /**
     * 重载服务
     */
    public function reload(): void
    {
    }

    /**
     * 获取客户端地址
     */
    public function getClientAddress(string|int $clientId): IPEndPoint
    {
        return new IPEndPoint('127.0.0.1', 0);
    }
}
