<?php
namespace Imi\Db\Query;

use Imi\Model\Model;
use Imi\Event\IEvent;
use Imi\Bean\BeanFactory;
use Imi\Model\Event\ModelEvents;
use Imi\Db\Interfaces\IStatement;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Model\Event\Param\AfterQueryEventParam;
use Imi\Db\Statement\StatementManager;

class Result implements IResult
{
    /**
     * Statement
     * @var IStatement
     */
    private $statement;

    /**
     * 是否执行成功
     * @var bool
     */
    private $isSuccess;

    /**
     * 查询结果类的类名，为null则为数组
     * @var string
     */
    private $modelClass;

    /**
     * 记录列表
     *
     * @var array
     */
    private $statementRecords = [];

    /**
     * Undocumented function
     *
     * @param \Imi\Db\Interfaces\IStatement $statement
     * @param string|null $modelClass
     */
    public function __construct($statement, $modelClass = null)
    {
        $this->modelClass = $modelClass;
        if($statement instanceof IStatement)
        {
            $this->statement = $statement;
            $this->isSuccess = '00000' === $this->statement->errorCode();
            $this->statementRecords = $this->statement->fetchAll();
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
     * SQL是否执行成功
     * @return boolean
     */
    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * 获取最后插入的ID
     * @return string
     */
    public function getLastInsertId()
    {
        if(!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        return $this->statement->lastInsertId();
    }

    /**
     * 获取影响行数
     * @return int
     */
    public function getAffectedRows()
    {
        if(!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        return $this->statement->rowCount();
    }

    /**
     * 返回一行数据，数组或对象，失败返回null
     * @param string $className 实体类名，为null则返回数组
     * @return mixed|null
     */
    public function get($className = null)
    {
        if(!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        $record = $this->statementRecords[0] ?? null;
        if(!$record)
        {
            return null;
        }

        if(null === $className)
        {
            $className = $this->modelClass;
        }
        if(null === $className)
        {
            return $record;
        }
        else
        {
            if(is_subclass_of($className, Model::class))
            {
                $object = BeanFactory::newInstance($className, $record);
            }
            else
            {
                $object = BeanFactory::newInstance($className);
                foreach($record as $k => $v)
                {
                    $object->$k = $v;
                }
            }
            if(is_subclass_of($object, IEvent::class))
            {
                $className = BeanFactory::getObjectClass($object);
                $object->trigger(ModelEvents::AFTER_QUERY, [
                    'model'      =>  $object,
                ], $object, AfterQueryEventParam::class);
            }
            return $object;
        }
    }

    /**
     * 返回数组，失败返回null
     * @param string $className 实体类名，为null则数组每个成员为数组
     * @return array|null
     */
    public function getArray($className = null)
    {
        if(!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }

        if(null === $className)
        {
            $className = $this->modelClass;
        }
        if(null === $className)
        {
            return $this->statementRecords;
        }
        else
        {
            $list = [];
            $isModelClass = is_subclass_of($className, Model::class);
            $supportIEvent = is_subclass_of($className, IEvent::class);
            foreach($this->statementRecords as $item)
            {
                if($isModelClass)
                {
                    $object = BeanFactory::newInstance($className, $item);
                }
                else
                {
                    $object = $item;
                }
                if($supportIEvent)
                {
                    $object->trigger(ModelEvents::AFTER_QUERY, [
                        'model'      =>  $object,
                    ], $object, AfterQueryEventParam::class);
                }
                $list[] = $object;
            }
            return $list;
        }
    }

    /**
     * 获取一列数据
     * @return array
     */
    public function getColumn($column = 0)
    {
        if(!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        if(isset($this->statementRecords[0]))
        {
            if(is_numeric($column))
            {
                $keys = array_keys($this->statementRecords[0]);
                return array_column($this->statementRecords, $keys[$column]);
            }
            else
            {
                return array_column($this->statementRecords, $column);
            }
        }
        return [];
    }

    /**
     * 获取标量结果
     * @param integer|string $columnKey
     * @return mixed
     */
    public function getScalar($columnKey = 0)
    {
        if(!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        $record = $this->statementRecords[0] ?? null;
        if($record)
        {
            if(is_numeric($columnKey))
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
     * 获取记录行数
     * @return int
     */
    public function getRowCount()
    {
        if(!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        return count($this->statementRecords);
    }

    /**
     * 获取执行的SQL语句
     *
     * @return string
     */
    public function getSql()
    {
        return $this->statement->getSql();
    }

    /**
     * 获取结果集对象
     *
     * @return \Imi\Db\Interfaces\IStatement
     */
    public function getStatement(): IStatement
    {
        return $this->statement;
    }

}