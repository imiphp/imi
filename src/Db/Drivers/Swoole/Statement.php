<?php
namespace Imi\Db\Drivers\Swoole;

use Imi\Db\Interfaces\IDb;
use Imi\Db\Drivers\BaseStatement;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IStatement;

/**
 * Swoole Coroutine MySQL 驱动 Statement
 */
class Statement extends BaseStatement implements IStatement
{
    /**
     * Statement
     * @var \Swoole\Coroutine\MySQL\Statement|array
     */
    protected $statement;

    /**
     * 数据
     * @var array
     */
    protected $data;

    /**
     * 数据库操作对象
     * @var IDb
     */
    protected $db;

    /**
     * 绑定数据
     *
     * @var array
     */
    protected $bindValues = [];

    /**
     * 结果数组
     *
     * @var array
     */
    protected $result = [];

    /**
     * 最后执行过的SQL语句
     *
     * @var string
     */
    protected $lastSql = '';

    /**
     * SQL 参数映射
     *
     * @var array
     */
    protected $sqlParamsMap;

    public function __construct(IDb $db, $statement, string $originSql, ?array $sqlParamsMap = null)
    {
        $this->db = $db;
        $this->statement = $statement;
        if(is_array($statement))
        {
            $this->result = $statement;
        }
        $this->lastSql = $originSql;
        $this->sqlParamsMap = $sqlParamsMap;
    }

    /**
     * 获取数据库操作对象
     * @return IDb
     */
    public function getDb(): IDb
    {
        return $this->db;
    }

    /**
     * 绑定一列到一个 PHP 变量
     * @param mixed $column
     * @param mixed $param
     * @param integer $type
     * @param integer $maxLen
     * @param mixed $driverData
     * @return boolean
     */
    public function bindColumn($column, &$param, int $type = null, int $maxLen = null, $driverData = null): bool
    {
        $this->bindValues[$column] = $param;
        return true;
    }

    /**
     * 绑定一个参数到指定的变量名
     * @param mixed $parameter
     * @param mixed $variable
     * @param integer $dataType
     * @param integer $length
     * @param mixed $driverOptions
     * @return boolean
     */
    public function bindParam($parameter, &$variable, int $dataType = \PDO::PARAM_STR, int $length = null, $driverOptions = null): bool
    {
        $this->bindValues[$parameter] = $variable;
        return true;
    }

    /**
     * 把一个值绑定到一个参数
     * @param mixed $parameter
     * @param mixed $value
     * @param integer $dataType
     * @return boolean
     */
    public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool
    {
        $this->bindValues[$parameter] = $value;
        return true;
    }

    /**
     * 关闭游标，使语句能再次被执行。
     * @return boolean
     */
    public function closeCursor(): bool
    {
        return true;
    }

    /**
     * 返回结果集中的列数
     * @return int
     */
    public function columnCount(): int
    {
        return count($this->result[0] ?? []);
    }
    
    /**
     * 返回错误码
     * @return mixed
     */
    public function errorCode()
    {
        return is_array($this->statement) ? $this->db->errorCode() : $this->statement->errno;
    }
    
    /**
     * 返回错误信息
     * @return array
     */
    public function errorInfo(): string
    {
        return is_array($this->statement) ? $this->db->errorInfo() : $this->statement->error;
    }

    /**
     * 获取SQL语句
     * @return string
     */
    public function getSql()
    {
        return $this->lastSql;
    }

    /**
     * 执行一条预处理语句
     * @param array $inputParameters
     * @return boolean
     */
    public function execute(array $inputParameters = null): bool
    {
        $statement = $this->statement;
        if(is_array($statement))
        {
            $result = $this->db->getInstance()->query($this->lastSql);
            if(false === $result)
            {
                throw new DbException('sql query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
            }
        }
        else
        {
            $bindValues = $this->bindValues;
            $this->bindValues = [];
            if(null !== $inputParameters)
            {
                $sqlParamsMap = $this->sqlParamsMap;
                if($sqlParamsMap)
                {
                    foreach($this->sqlParamsMap as $index => $paramName)
                    {
                        if(isset($inputParameters[$paramName]))
                        {
                            $bindValues[$index] = $inputParameters[$paramName];
                        }
                    }
                }
                else
                {
                    foreach($inputParameters as $k => $v)
                    {
                        $bindValues[$k] = $v;
                    }
                }
            }
            if($bindValues)
            {
                ksort($bindValues);
                $bindValues = array_values($bindValues);
            }
            $result = $statement->execute($bindValues);
            if(true === $result)
            {
                $result = $statement->fetchAll();
                if(false === $result)
                {
                    $result = [];
                }
            }
            else if(false === $result)
            {
                throw new DbException('sql query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
            }
        }
        $this->result = (true === $result ? [] : $result);
        return true;
    }

    /**
     * 从结果集中获取下一行
     * @param integer $fetchStyle
     * @param integer $cursorOrientation
     * @param integer $cursorOffset
     * @return mixed
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0)
    {
        $result = current($this->result);
        next($this->result);
        return $result;
    }

    /**
     * 返回一个包含结果集中所有行的数组
     * @param integer $fetchStyle
     * @param mixed $fetchArgument
     * @return array
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = []): array
    {
        return $this->result;
    }

    /**
     * 从结果集中的下一行返回单独的一列，不存在返回null
     * @param integer|string $columnKey
     * @return mixed
     */
    public function fetchColumn($columnKey = 0)
    {
        $row = current($this->result);
        next($this->result);
        if(isset($row[$columnKey]))
        {
            return $row[$columnKey];
        }
        else if(is_numeric($columnKey))
        {
            return array_values($row)[$columnKey] ?? null;
        }
        return null;
    }
    
    /**
     * 获取下一行并作为一个对象返回。
     * @param string $class_name
     * @param array $ctor_args
     * @return mixed
     */
    public function fetchObject(string $className = 'stdClass', array $ctorArgs = null)
    {
        $row = current($this->result);
        if(false === $row)
        {
            return null;
        }
        next($this->result);
        if('stdClass' === $className)
        {
            return (object)$row;
        }
        $result = new $className;
        foreach($row as $k => $v)
        {
            $result->$k = $v;
        }
        return $result;
    }

    /**
     * 检索一个语句属性
     * @param mixed $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return null;
    }

    /**
     * 设置属性
     * @param mixed $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value): bool
    {
        return true;
    }

    /**
     * 在一个多行集语句句柄中推进到下一个行集
     * @return boolean
     */
    public function nextRowset(): bool
    {
        return !!next($this->result);
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name
     * @return string
     */
    public function lastInsertId(string $name = null)
    {
        return is_array($this->statement) ? $this->db->lastInsertId() : $this->statement->insert_id;
    }

    /**
     * 返回受上一个 SQL 语句影响的行数
     * @return int
     */
    public function rowCount(): int
    {
        return is_array($this->statement) ? $this->db->rowCount() : $this->statement->affected_rows;
    }

    /**
     * 获取原对象实例
     * @return object
     */
    public function getInstance()
    {
        return $this->statement;
    }

    public function current()
    {
        return current($this->result);
    }

    public function key()
    {
        return key($this->result);
    }

    public function next()
    {
        return next($this->result);
    }

    public function rewind()
    {
        return reset($this->result);
    }

    public function valid()
    {
        return false !== $this->current();
    }

}