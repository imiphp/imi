<?php
namespace Imi\Db\Transaction;

trait TTransaction
{
    private $transactionLevels = 0;

    /**
     * 启动一个事务
     * @return boolean
     */
    public function beginTransaction(): bool
    {
        ++$this->transactionLevels;
        return true;
    }

    /**
     * 提交一个事务
     * @return boolean
     */
    public function commit(): bool
    {
        $this->transactionLevels = 0;
        return true;
    }

    /**
     * 回滚事务
     * 支持设置回滚事务层数，如果不设置则为全部回滚
     * @param int $levels
     * @return boolean
     */
    public function rollBack($levels = null): bool
    {
        if(null === $levels)
        {
            $this->transactionLevels = 0;
        }
        else
        {
            $this->transactionLevels -= $levels;
        }
        return true;
    }

    /**
     * 获取事务层数
     *
     * @return int
     */
    public function getTransactionLevels(): int
    {
        return $this->transactionLevels;
    }
}