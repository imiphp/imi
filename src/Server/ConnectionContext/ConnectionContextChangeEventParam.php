<?php

declare(strict_types=1);

namespace Imi\Server\ConnectionContext;

/**
 * 连接上下文改变事件参数.
 */
class ConnectionContextChangeEventParam
{
    /**
     * 连接标识符.
     *
     * @var int|string
     */
    private $clientId = 0;

    /**
     * 服务器名.
     */
    private string $serverName = '';

    /**
     * @param int|string $clientId
     */
    public function __construct($clientId, string $serverName)
    {
        $this->clientId = $clientId;
        $this->serverName = $serverName;
    }

    /**
     * Get 连接标识符.
     *
     * @return int|string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get 服务器名.
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }
}
