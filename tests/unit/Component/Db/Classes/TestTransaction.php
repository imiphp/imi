<?php

declare(strict_types=1);

namespace Imi\Test\Component\Db\Classes;

use Imi\Db\Annotation\Transaction;
use Imi\Db\Db;

class TestTransaction
{
    /**
     * 事务嵌套.
     */
    #[Transaction(type: 'Nesting')]
    public function nestingCommit(): void
    {
        $this->__listen();
    }

    /**
     * 事务嵌套.
     */
    #[Transaction(type: 'Nesting')]
    public function nestingCommit2(): void
    {
        $this->__listen();
        $this->nestingCommit();
    }

    /**
     * 事务嵌套.
     */
    #[Transaction(type: 'Nesting')]
    public function nestingRollback(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 事务嵌套.
     */
    #[Transaction(type: 'Nesting')]
    public function nestingRollback2(): void
    {
        $this->__listen();
        $this->nestingRollback();
    }

    /**
     * 该方法必须在事务中被调用.
     */
    #[Transaction(type: 'requirement')]
    public function requirementCommit(): void
    {
        $this->__listen();
    }

    /**
     * 该方法必须在事务中被调用.
     */
    #[Transaction(type: 'requirement')]
    public function requirementRollback(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 如果当前不在事务中则开启事务
     */
    #[Transaction]
    public function autoCommit(): void
    {
        $this->__listen();
    }

    /**
     * 如果当前不在事务中则开启事务
     */
    #[Transaction]
    public function autoRollback(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 回滚1层事务
     */
    #[Transaction(type: 'Nesting', rollbackType: 'part')]
    public function rollbackPart1(): void
    {
        $this->__listen();
        throw new \RuntimeException('gg');
    }

    /**
     * 回滚所有事务
     */
    #[Transaction(type: 'Nesting', rollbackType: 'part', rollbackLevels: null)]
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
