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
    private IDb $db;

    public function __construct(\Imi\Pool\Interfaces\IPool $pool, IDb $db)
    {
        parent::__construct($pool);
        $this->db = $db;
    }

    /**
     * 打开
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
     * 关闭.
     */
    public function close(): void
    {
        $this->db->close();
    }

    /**
     * 获取对象实例.
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->db;
    }

    /**
     * 重置资源，当资源被使用后重置一些默认的设置.
     */
    public function reset(): void
    {
        $db = $this->db;
        // 如果在事务中，则回滚
        if ($db->isConnected() && $db->inTransaction())
        {
            $db->rollBack();
        }
    }

    /**
     * 检查资源是否可用.
     */
    public function checkState(): bool
    {
        return $this->db->ping();
    }
}
