<?php

namespace Imi\Server\ConnectContext;

/**
 * 连接上下文改变事件参数.
 */
class ConnectContextChangeEventParam
{
    /**
     * 连接标识符.
     *
     * @var int
     */
    private int $fd;

    /**
     * 服务器名.
     *
     * @var string
     */
    private string $serverName;

    public function __construct(int $fd, string $serverName)
    {
        $this->fd = $fd;
        $this->serverName = $serverName;
    }

    /**
     * Get 连接标识符.
     *
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * Get 服务器名.
     *
     * @return string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }
}
