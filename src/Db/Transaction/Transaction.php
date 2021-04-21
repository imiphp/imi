<?php

namespace Imi\Db\Transaction;

use Imi\Event\TEvent;

class Transaction
{
    use TEvent;

    /**
     * 事务层级计数.
     *
     * @var int
     */
    private $transactionLevels = 0;

    /**
     * 事务总数.
     *
     * @var int
     */
    private $transactionCount = 0;

    /**
     * 启动一个事务
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        ++$this->transactionLevels;
        ++$this->transactionCount;

        return true;
    }

    /**
     * 提交一个事务
     *
     * @return bool
     */
    public function commit(): bool
    {
        $offEvents = [];
        $levels = $this->transactionLevels;
        $this->transactionLevels = 0;
        $i = $levels;
        $prefixName = 'transaction.' . $this->transactionCount . '.';
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
     *
     * @param int $levels
     *
     * @return bool
     */
    public function rollBack($levels = null): bool
    {
        $offEvents = [];
        $transactionLevels = &$this->transactionLevels;
        if (null === $levels)
        {
            $final = 0;
        }
        else
        {
            $final = $transactionLevels - $levels;
        }
        $i = $transactionLevels;
        $prefixName = 'transaction.' . $this->transactionCount . '.';
        try
        {
            for (; $i >= $final; --$i)
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
            for (; $i >= $final; --$i)
            {
                $offEvents[] = $prefixName . $i . '.commit';
                $offEvents[] = $prefixName . $i . '.rollback';
            }
            throw $th;
        }
        finally
        {
            $transactionLevels = $final;
            $this->off($offEvents);
        }

        return true;
    }

    /**
     * 获取事务层数.
     *
     * @return int
     */
    public function getTransactionLevels(): int
    {
        return $this->transactionLevels;
    }

    /**
     * 监听事务提交事件.
     *
     * @param callable $callable
     *
     * @return void
     */
    public function onTransactionCommit(callable $callable)
    {
        $this->one('transaction.' . $this->transactionCount . '.' . $this->transactionLevels . '.commit', $callable);
    }

    /**
     * 监听事务回滚事件.
     *
     * @param callable $callable
     *
     * @return void
     */
    public function onTransactionRollback(callable $callable)
    {
        $this->one('transaction.' . $this->transactionCount . '.' . $this->transactionLevels . '.rollback', $callable);
    }
}
