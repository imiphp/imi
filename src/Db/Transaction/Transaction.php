<?php
namespace Imi\Db\Transaction;

use Imi\Event\TEvent;

class Transaction
{
    use TEvent;

    /**
     * 事务层级计数
     *
     * @var integer
     */
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
        $offEvents = [];
        $levels = &$this->transactionLevels;
        for($i = $levels; $i >= 0; --$i)
        {
            $this->trigger('transaction.' . $i . '.commit', [
                'db'    =>  $this,
                'level' =>  $i,
            ]);
            $offEvents[] = 'transaction.' . $i . '.rollback';
        }
        $this->off($offEvents);
        $levels = 0;
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
        $offEvents = [];
        $transactionLevels = &$this->transactionLevels;
        if(null === $levels)
        {
            for($i = $transactionLevels; $i >= 0; --$i)
            {
                $this->trigger('transaction.' . $i . '.rollback', [
                    'db'    =>  $this,
                    'level' =>  $i,
                ]);
                $offEvents[] = 'transaction.' . $i . '.commit';
            }
            $transactionLevels = 0;
        }
        else
        {
            $final = $transactionLevels - $levels;
            for($i = $transactionLevels; $i >= $final; --$i)
            {
                $this->trigger('transaction.' . $i . '.rollback', [
                    'db'    =>  $this,
                    'level' =>  $i,
                ]);
                $offEvents[] = 'transaction.' . $i . '.commit';
            }
            $transactionLevels = $final;
        }
        $this->off($offEvents);
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

    /**
     * 监听事务提交事件
     *
     * @param callable $callable
     * @return void
     */
    public function onTransactionCommit(callable $callable)
    {
        $this->one('transaction.' . $this->transactionLevels . '.commit', $callable);
    }

    /**
     * 监听事务回滚事件
     *
     * @param callable $callable
     * @return void
     */
    public function onTransactionRollback(callable $callable)
    {
        $this->one('transaction.' . $this->transactionLevels . '.rollback', $callable);
    }

}