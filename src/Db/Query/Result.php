<?php

declare(strict_types=1);

namespace Imi\Db\Query;

use Imi\Bean\BeanFactory;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Statement\StatementManager;
use Imi\Event\IEvent;
use Imi\Model\Event\ModelEvents;
use Imi\Model\Event\Param\AfterQueryEventParam;
use Imi\Model\Model;

class Result implements IResult
{
    /**
     * Statement.
     */
    protected IStatement $statement;

    /**
     * 是否执行成功
     */
    protected bool $isSuccess = false;

    /**
     * 查询结果类的类名，为null则为数组.
     */
    protected ?string $modelClass = null;

    /**
     * 记录列表.
     */
    protected array $statementRecords = [];

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
            if ($statement->columnCount() > 0)
            {
                $this->statementRecords = $statement->fetchAll();
            }
        }
        else
        {
            $this->isSuccess = false;
        }
    }

    public function __destruct()
    {
        StatementManager::unUsing($this->statement);
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
    public function getLastInsertId()
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }

        return (int) $this->statement->lastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function getAffectedRows(): int
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }

        return $this->statement->rowCount();
    }

    /**
     * {@inheritDoc}
     */
    public function get(?string $className = null)
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        $record = $this->statementRecords[0] ?? null;
        if (!$record)
        {
            return null;
        }

        if (null === $className)
        {
            $className = $this->modelClass;
        }
        if (null === $className)
        {
            return $record;
        }
        else
        {
            if (is_subclass_of($className, Model::class))
            {
                $object = $className::createFromRecord($record);
            }
            else
            {
                $object = BeanFactory::newInstance($className);
                foreach ($record as $k => $v)
                {
                    $object->$k = $v;
                }
            }
            if (is_subclass_of($object, IEvent::class))
            {
                $object->trigger(ModelEvents::AFTER_QUERY, [
                    'model'      => $object,
                ], $object, AfterQueryEventParam::class);
            }

            return $object;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getArray(?string $className = null): array
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }

        if (null === $className)
        {
            $className = $this->modelClass;
        }
        if (null === $className)
        {
            return $this->statementRecords;
        }
        elseif (is_subclass_of($className, Model::class))
        {
            $list = [];
            foreach ($this->statementRecords as $item)
            {
                $object = $className::createFromRecord($item);
                $object->trigger(ModelEvents::AFTER_QUERY, [
                    'model' => $object,
                ], $object, AfterQueryEventParam::class);
                $list[] = $object;
            }

            return $list;
        }
        else
        {
            $list = [];
            foreach ($this->statementRecords as $item)
            {
                $list[] = new $className($item);
            }

            return $list;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getColumn($column = 0): array
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        $statementRecords = &$this->statementRecords;
        if (isset($statementRecords[0]))
        {
            if (is_numeric($column))
            {
                $keys = array_keys($statementRecords[0]);

                return array_column($statementRecords, $keys[$column]);
            }
            else
            {
                return array_column($statementRecords, $column);
            }
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getScalar($columnKey = 0)
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        $record = $this->statementRecords[0] ?? null;
        if ($record)
        {
            if (is_numeric($columnKey))
            {
                $keys = array_keys($record);

                return $record[$keys[$columnKey]];
            }
            else
            {
                return $record[$columnKey];
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getRowCount(): int
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }

        return \count($this->statementRecords);
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
}
