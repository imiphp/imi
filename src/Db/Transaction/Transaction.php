<?php

declare(strict_types=1);

namespace Imi\Db\Transaction;

use Imi\Event\TEvent;

class Transaction
{
    use TEvent;

    /**
     * 事务层级计数.
     */
    private int $transactionLevels = 0;

    /**
     * 事务总数.
     */
    private int $transactionCount = 0;

    /**
     * 当前事务.
     */
    private int $currentTransaction = 0;

    public function init(): void
    {
        $this->transactionLevels = $this->transactionCount = $this->currentTransaction = 0;
        $this->getEventController()->getEventDispatcher()->getListenerProvider()->clearListeners();
    }

    /**
     * 启动一个事务
     */
    public function beginTransaction(): bool
    {
        if (1 === ++$this->transactionLevels)
        {
            $this->currentTransaction = ++$this->transactionCount;
        }

        return true;
    }

    /**
     * 提交一个事务
     */
    public function commit(): bool
    {
        $offEvents = [];
        $levels = $this->transactionLevels;
        $this->transactionLevels = 0;
        $i = $levels;
        $currentTransaction = &$this->currentTransaction;
        $prefixName = 'transaction.' . $currentTransaction . '.';
        $currentTransaction = 0;
        try
        {
            for (; $i >= 0; --$i)
            {
                $this->trigger($prefixName . $i . '.commit', [
                    'db'    => $this,
                    'level' => $i,
                ]);
                $offEvents[] = $prefixName . $i . '.rollback';
            }
        }
        catch (\Throwable $th)
        {
            for (; $i >= 0; --$i)
            {
                $offEvents[] = $prefixName . $i . '.commit';
                $offEvents[] = $prefixName . $i . '.rollback';
            }
            throw $th;
        }
        finally
        {
            $this->off($offEvents);
        }

        return true;
    }

    /**
     * 回滚事务
     * 支持设置回滚事务层数，如果不设置则为全部回滚.
     */
    public function rollBack(?int $levels = null): bool
    {
        $offEvents = [];
        $transactionLevels = &$this->transactionLevels;
        $i = $transactionLevels;
        $currentTransaction = &$this->currentTransaction;
        $prefixName = 'transaction.' . $currentTransaction . '.';
        if (null === $levels)
        {
            $transactionLevels = 0;
            $currentTransaction = 0;
        }
        else
        {
            $transactionLevels -= $levels;
            if ($transactionLevels <= 0)
            {
                $currentTransaction = 0;
            }
        }
        try
        {
            for (; $i >= $transactionLevels; --$i)
            {
                $this->trigger($prefixName . $i . '.rollback', [
                    'db'    => $this,
                    'level' => $i,
                ]);
                $offEvents[] = $prefixName . $i . '.commit';
            }
        }
        catch (\Throwable $th)
        {
            for (; $i >= $transactionLevels; --$i)
            {
                $offEvents[] = $prefixName . $i . '.commit';
                $offEvents[] = $prefixName . $i . '.rollback';
            }
            throw $th;
        }
        finally
        {
            $this->off($offEvents);
        }

        return true;
    }

    /**
     * 获取事务层数.
     */
    public function getTransactionLevels(): int
    {
        return $this->transactionLevels;
    }

    /**
     * 监听事务提交事件.
     */
    public function onTransactionCommit(callable $callable): void
    {
        $this->one('transaction.' . ($this->currentTransaction ?: ($this->transactionCount + 1)) . '.' . $this->transactionLevels . '.commit', $callable);
    }

    /**
     * 监听事务回滚事件.
     */
    public function onTransactionRollback(callable $callable): void
    {
        $this->one('transaction.' . ($this->currentTransaction ?: ($this->transactionCount + 1)) . '.' . $this->transactionLevels . '.rollback', $callable);
    }
}
