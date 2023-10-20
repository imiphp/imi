<?php

declare(strict_types=1);

namespace Imi\Test\Component\Db\Classes;

use Imi\Db\Annotation\RollbackType;
use Imi\Db\Annotation\Transaction;
use Imi\Db\Annotation\TransactionType;
use Imi\Db\Db;

class TestTransaction
{
    /**
     * 事务嵌套.
     *
     * @Transaction(type=TransactionType::NESTING)
     */
    public function nestingCommit(): void
    {
        $this->__listen();
    }

    /**
     * 事务嵌套.
     *
     * @Transaction(type=TransactionType::NESTING)
     */
    public function nestingCommit2(): void
    {
        $this->__listen();
        $this->nestingCommit();
    }

    /**
     * 事务嵌套.
     *
     * @Transaction(type=TransactionType::NESTING)
     */
    public function nestingRollback(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 事务嵌套.
     *
     * @Transaction(type=TransactionType::NESTING)
     */
    public function nestingRollback2(): void
    {
        $this->__listen();
        $this->nestingRollback();
    }

    /**
     * 该方法必须在事务中被调用.
     *
     * @Transaction(type=TransactionType::REQUIREMENT)
     */
    public function requirementCommit(): void
    {
        $this->__listen();
    }

    /**
     * 该方法必须在事务中被调用.
     *
     * @Transaction(type=TransactionType::REQUIREMENT)
     */
    public function requirementRollback(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 如果当前不在事务中则开启事务
     *
     * @Transaction(type=TransactionType::AUTO)
     */
    public function autoCommit(): void
    {
        $this->__listen();
    }

    /**
     * 如果当前不在事务中则开启事务
     *
     * @Transaction(type=TransactionType::AUTO)
     */
    public function autoRollback(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 回滚1层事务
     *
     * @Transaction(type=TransactionType::NESTING, rollbackType=RollbackType::PART, rollbackLevels=1)
     */
    public function rollbackPart1(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 回滚所有事务
     *
     * @Transaction(type=TransactionType::NESTING, rollbackType=RollbackType::PART, rollbackLevels=null)
     */
    public function rollbackPartAll(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    private function __listen(): void
    {
        $transaction = Db::getInstance()->getTransaction();
        $transaction->onTransactionCommit(static function (): void {
        });
        $transaction->onTransactionRollback(static function (): void {
        });
    }
}
