<?php

declare(strict_types=1);

namespace Imi\Db\Query\Result;

use Imi\Bean\BeanFactory;
use Imi\Db\Interfaces\IStatement;
use Imi\Event\IEvent;
use Imi\Model\Event\ModelEvents;
use Imi\Model\Event\Param\AfterQueryEventParam;
use Imi\Model\Model;
use function is_subclass_of;

class CursorResult implements \IteratorAggregate
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
     * @return \Generator|iterable<array>|iterable<Model>
     */
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

                yield $object;
            }
        }
    }
}
