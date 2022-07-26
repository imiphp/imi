<?php

declare(strict_types=1);

namespace Imi\Db\Pool;

use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IDb;
use Imi\Pool\BasePoolResource;

/**
 * 数据库连接池的资源.
 */
class DbResource extends BasePoolResource
{
    /**
     * db对象
     */
    private ?IDb $db = null;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, IDb $db)
    {
        parent::__construct($pool);
        $this->db = $db;
    }

    /**
     * {@inheritDoc}
     */
    public function open(): bool
    {
        $db = $this->db;
        if (!$db->open())
        {
            throw new DbException('Db connect error: [' . $db->errorCode() . '] ' . $db->errorInfo());
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->db->close();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance()
    {
        return $this->db;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        $db = $this->db;
        // 如果在事务中，则回滚
        if ($db->inTransaction() && $db->isConnected())
        {
            $db->rollBack();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function checkState(): bool
    {
        return $this->db->ping();
    }

    /**
     * {@inheritDoc}
     */
    public function isOpened(): bool
    {
        return $this->db->isConnected();
    }
}
