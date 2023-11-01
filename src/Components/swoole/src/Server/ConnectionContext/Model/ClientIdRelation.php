<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\ConnectionContext\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\MemoryTable;
use Imi\Swoole\Model\MemoryTableModel;

/**
 * clientId关联表，主键为clientId.
 */
#[MemoryTable(name: 'imiClientIdRelation')]
class ClientIdRelation extends MemoryTableModel
{
    /**
     * worker进程ID.
     */
    #[Column(name: 'workerId', type: \Imi\Cli\ArgType::INT)]
    protected int $workerId = 0;

    /**
     * 服务器名.
     */
    #[Column(name: 'serverName', type: \Imi\Cli\ArgType::STRING, length: 128)]
    protected string $serverName = '';

    /**
     * Get worker进程ID.
     */
    public function getWorkerId(): int
    {
        return $this->workerId;
    }

    /**
     * Set worker进程ID.
     *
     * @param int $workerId worker进程ID
     */
    public function setWorkerId(int $workerId): self
    {
        $this->workerId = $workerId;

        return $this;
    }

    /**
     * Get 服务器名.
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * Set 服务器名.
     *
     * @param string $serverName 服务器名
     */
    public function setServerName(string $serverName): self
    {
        $this->serverName = $serverName;

        return $this;
    }
}
