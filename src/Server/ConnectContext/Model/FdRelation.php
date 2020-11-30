<?php

declare(strict_types=1);

namespace Imi\Server\ConnectContext\Model;

use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\MemoryTable;
use Imi\Model\MemoryTableModel;

/**
 * fd关联表，主键为fd.
 *
 * @MemoryTable(name="imiFdRelation")
 */
class FdRelation extends MemoryTableModel
{
    /**
     * worker进程ID.
     *
     * @Column(name="workerId", type="int")
     *
     * @var int
     */
    protected int $workerId;

    /**
     * 服务器名.
     *
     * @Column(name="serverName", type="string", length=128)
     *
     * @var string
     */
    protected string $serverName;

    /**
     * Get worker进程ID.
     *
     * @return int
     */
    public function getWorkerId(): int
    {
        return $this->workerId;
    }

    /**
     * Set worker进程ID.
     *
     * @param int $workerId worker进程ID
     *
     * @return self
     */
    public function setWorkerId(int $workerId): self
    {
        $this->workerId = $workerId;

        return $this;
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

    /**
     * Set 服务器名.
     *
     * @param string $serverName 服务器名
     *
     * @return self
     */
    public function setServerName(string $serverName): self
    {
        $this->serverName = $serverName;

        return $this;
    }
}
