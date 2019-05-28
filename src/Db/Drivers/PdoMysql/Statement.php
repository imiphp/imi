<?php
namespace Imi\Db\Drivers\PdoMysql;

use Imi\Db\Interfaces\IDb;
use Imi\Util\LazyArrayObject;
use Imi\Db\Drivers\BaseStatement;
use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IStatement;

/**
 * PDO MySQL驱动Statement
 * 
 * @property-read string $queryString
 */
class Statement extends BaseStatement implements IStatement
{
    /**
     * \PDOStatement
     * @var \PDOStatement
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

    public function __construct(IDb $db, $statement)
    {
        $this->db = $db;
        $this->statement = $statement;
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
        return $this->statement->bindColumn($column, $param, $type, $maxLen, $driverData);
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
        return $this->statement->bindParam($parameter, $variable, $dataType, $length, $driverOptions);
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
        return $this->statement->bindValue($parameter, $value, $dataType);
    }

    /**
     * 关闭游标，使语句能再次被执行。
     * @return boolean
     */
    public function closeCursor(): bool
    {
        return $this->statement->closeCursor();
    }

    /**
     * 返回结果集中的列数
     * @return int
     */
    public function columnCount(): int
    {
        return $this->statement->columnCount();
    }
    
    /**
     * 返回错误码
     * @return mixed
     */
    public function errorCode()
    {
        return $this->statement->errorCode();
    }
    
    /**
     * 返回错误信息
     * @return array
     */
    public function errorInfo(): string
    {
        $errorInfo = $this->statement->errorInfo();
        return !isset($errorInfo[0]) || 0 == $errorInfo[0] ? '' : implode(' ', $errorInfo);
    }

    /**
     * 获取SQL语句
     * @return string
     */
    public function getSql()
    {
        return $this->statement->queryString;
    }

    /**
     * 执行一条预处理语句
     * @param array $inputParameters
     * @return boolean
     */
    public function execute(array $inputParameters = null): bool
    {
        $result = $this->statement->execute($inputParameters);
        if(!$result)
        {
            throw new DbException('sql query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
        }
        return $result;
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
        return $this->statement->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * 返回一个包含结果集中所有行的数组
     * @param integer $fetchStyle
     * @param mixed $fetchArgument
     * @return array
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = array()): array
    {
        if(null === $fetchArgument)
        {
            return $this->statement->fetchAll($fetchStyle);
        }
        else if([] === $ctorArgs)
        {
            return $this->statement->fetchAll($fetchStyle, $fetchArgument);
        }
        else
        {
            return $this->statement->fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
        }
    }

    /**
     * 从结果集中的下一行返回单独的一列，不存在返回null
     * @param integer|string $columnKey
     * @return mixed
     */
    public function fetchColumn($columnKey = 0)
    {
        return $this->statement->fetchColumn($columnKey);
    }
    
    /**
     * 获取下一行并作为一个对象返回。
     * @param string $class_name
     * @param array $ctor_args
     * @return mixed
     */
    public function fetchObject(string $className = "stdClass", array $ctorArgs = null)
    {
        return $this->statement->fetchObject($className, $ctorArgs);
    }

    /**
     * 检索一个语句属性
     * @param mixed $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return $this->statement->getAttribute($attribute);
    }

    /**
     * 设置属性
     * @param mixed $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value): bool
    {
        return $this->statement->setAttribute($attribute, $value);
    }

    /**
     * 在一个多行集语句句柄中推进到下一个行集
     * @return boolean
     */
    public function nextRowset(): bool
    {
        return $this->statement->nextRowset();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name
     * @return string
     */
    public function lastInsertId(string $name = null)
    {
        return $this->db->lastInsertId($name);
    }

    /**
     * 返回受上一个 SQL 语句影响的行数
     * @return int
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
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
        return current($this->statement);
    }

    public function key()
    {
        return key($this->statement);
    }

    public function next()
    {
        return next($this->statement);
    }

    public function rewind()
    {
        return reset($this->statement);
    }

    public function valid()
    {
        return false !== $this->current();
    }
}