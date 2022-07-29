<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use Imi\Db\Interfaces\IStatement;
use Imi\Model\Model;

class CursorResult implements \IteratorAggregate
{
    use TResultEntityCreate;

    /**
     * Statement.
     */
    protected ?IStatement $statement = null;

    /**
     * 是否执行成功
     */
    protected bool $isSuccess = false;

    /**
     * 查询结果类的类名，为null则为数组.
     */
    protected ?string $modelClass = null;

    /**
     * @param \Imi\Db\Interfaces\IStatement|bool $statement
     */
    public function __construct($statement, ?string $modelClass = null)
    {
        $this->modelClass = $modelClass;
        if ($statement instanceof IStatement)
        {
            $this->statement = $statement;
            $this->isSuccess = '' === $statement->errorInfo();
        }
        else
        {
            $this->isSuccess = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * {@inheritDoc}
     */
    public function getSql(): string
    {
        return $this->statement->getSql();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatement(): IStatement
    {
        return $this->statement;
    }

    /**
     * @return \Traversable<int, array|Model>|\Generator|iterable<int, array|Model>
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }

        $className = $this->modelClass;

        while ($record = $this->statement->fetch())
        {
            if (null === $className)
            {
                yield $record;
            }
            else
            {
                yield $this->createEntity($className, $record);
            }
        }
    }
}
