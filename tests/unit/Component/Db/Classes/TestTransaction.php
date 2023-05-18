<?php

declare(strict_types=1);

namespace Imi\Test\Component\Db\Classes;

use Imi\Db\Annotation\RollbackType;
use Imi\Db\Annotation\Transaction;
use Imi\Db\Annotation\TransactionType;

class TestTransaction
{
    /**
     * 事务嵌套.
     *
     * @Transaction(type=TransactionType::NESTING)
     */
    public function nestingCommit(): void
    {
    }

    /**
     * 事务嵌套.
     *
     * @Transaction(type=TransactionType::NESTING)
     */
    public function nestingRollback(): void
    {
        throw new \RuntimeException('gg');
    }

    /**
     * 该方法必须在事务中被调用.
     *
     * @Transaction(type=TransactionType::REQUIREMENT)
     */
    public function requirementCommit(): void
    {
    }

    /**
     * 该方法必须在事务中被调用.
     *
     * @Transaction(type=TransactionType::REQUIREMENT)
     */
    public function requirementRollback(): void
    {
        throw new \RuntimeException('gg');
    }

    /**
     * 如果当前不在事务中则开启事务
     *
     * @Transaction(type=TransactionType::AUTO)
     */
    public function autoCommit(): void
    {
    }

    /**
     * 如果当前不在事务中则开启事务
     *
     * @Transaction(type=TransactionType::AUTO)
     */
    public function autoRollback(): void
    {
        throw new \RuntimeException('gg');
    }

    /**
     * 回滚1层事务
     *
     * @Transaction(type=TransactionType::NESTING, rollbackType=RollbackType::PART, rollbackLevels=1)
     */
    public function rollbackPart1(): void
    {
        throw new \RuntimeException('gg');
    }

    /**
     * 回滚所有事务
     *
     * @Transaction(type=TransactionType::NESTING, rollbackType=RollbackType::PART, rollbackLevels=null)
     */
    public function rollbackPartAll(): void
    {
        throw new \RuntimeException('gg');
    }
}
