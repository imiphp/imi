<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext;

/**
 * 连接上下文改变事件参数.
 */
class ConnectContextChangeEventParam
{
    /**
     * 连接标识符.
     */
    private int $fd = 0;

    /**
     * 服务器名.
     */
    private string $serverName = '';

    public function __construct(int $fd, string $serverName)
    {
        $this->fd = $fd;
        $this->serverName = $serverName;
    }

    /**
     * Get 连接标识符.
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * Get 服务器名.
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }
}
