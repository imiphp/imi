<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\Swoole;

use Imi\Db\Exception\DbException;
use Imi\Pgsql\Db\Contract\IPgsqlDb;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBaseStatement;

/**
 * Swoole Coroutine Pgsql 驱动 Statement.
 */
class Statement extends PgsqlBaseStatement implements IPgsqlStatement
{
    /**
     * @var mixed
     */
    protected $queryResult;

    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * 数据库操作对象
     */
    protected IPgsqlDb $db;

    /**
     * 绑定数据.
     */
    protected array $bindValues = [];

    /**
     * 结果数组.
     */
    protected array $result = [];

    /**
     * 最后执行过的SQL语句.
     */
    protected string $lastSql = '';

    /**
     * SQL 参数映射.
     */
    protected ?array $sqlParamsMap = null;

    /**
     * statement 名字.
     */
    protected ?string $statementName = null;

    /**
     * @param mixed $queryResult
     */
    public function __construct(IPgsqlDb $db, $queryResult, string $originSql, ?string $statementName = null, ?array $sqlParamsMap = null)
    {
        $this->db = $db;
        $this->queryResult = $queryResult;
        $this->lastSql = $originSql;
        $this->statementName = $statementName;
        $this->sqlParamsMap = $sqlParamsMap;
        if ($queryResult)
        {
            /** @var \Swoole\Coroutine\PostgreSQL $pgDb */
            $pgDb = $db->getInstance();
            if ($result = $pgDb->fetchAll($queryResult, \SW_PGSQL_ASSOC))
            {
                $this->result = $result;
            }
        }
    }

    /**
     * 获取数据库操作对象
     */
    public function getDb(): IPgsqlDb
    {
        return $this->db;
    }

    /**
     * 绑定一列到一个 PHP 变量.
     *
     * @param mixed $column
     * @param mixed $param
     * @param mixed $driverData
     */
    public function bindColumn($column, &$param, ?int $type = null, ?int $maxLen = null, $driverData = null): bool
    {
        $this->bindValues[$column] = $param;

        return true;
    }

    /**
     * 绑定一个参数到指定的变量名.
     *
     * @param mixed $parameter
     * @param mixed $variable
     * @param mixed $driverOptions
     */
    public function bindParam($parameter, &$variable, int $dataType = \PDO::PARAM_STR, ?int $length = null, $driverOptions = null): bool
    {
        $this->bindValues[$parameter] = $variable;

        return true;
    }

    /**
     * 把一个值绑定到一个参数.
     *
     * @param mixed $parameter
     * @param mixed $value
     */
    public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool
    {
        $this->bindValues[$parameter] = $value;

        return true;
    }

    /**
     * 关闭游标，使语句能再次被执行。
     */
    public function closeCursor(): bool
    {
        return true;
    }

    /**
     * 返回结果集中的列数.
     */
    public function columnCount(): int
    {
        return \count($this->result[0] ?? []);
    }

    /**
     * 返回错误码
     *
     * @return mixed
     */
    public function errorCode()
    {
        return $this->db->errorCode();
    }

    /**
     * 返回错误信息.
     */
    public function errorInfo(): string
    {
        return $this->errorInfo();
    }

    /**
     * 获取SQL语句.
     */
    public function getSql(): string
    {
        return $this->lastSql;
    }

    /**
     * 执行一条预处理语句.
     *
     * @param array $inputParameters
     */
    public function execute(array $inputParameters = null): bool
    {
        /** @var \Swoole\Coroutine\PostgreSQL $pgDb */
        $pgDb = $this->db->getInstance();
        // $this->queryResult = $queryResult = $pgDb->execute($this->statementName, $inputParameters ?? []);
        // if ($fetchResult = $pgDb->fetchAll($queryResult, \SW_PGSQL_ASSOC))
        // {
        //     $this->result = $fetchResult;
        // }

        // return (bool) $queryResult;
        // $statement = $this->statement;
        if (null === $this->statementName)
        {
            $this->queryResult = $queryResult = $pgDb->query($this->lastSql);
            if (false === $queryResult)
            {
                throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
            }
        }
        else
        {
            $bindValues = $this->bindValues ?? [];
            $this->bindValues = [];
            if (null !== $inputParameters)
            {
                $sqlParamsMap = $this->sqlParamsMap;
                if ($sqlParamsMap)
                {
                    foreach ($sqlParamsMap as $index => $paramName)
                    {
                        if (isset($inputParameters[$paramName]))
                        {
                            $bindValues[$index] = $inputParameters[$paramName];
                        }
                    }
                }
                elseif ($inputParameters)
                {
                    foreach ($inputParameters as $k => $v)
                    {
                        $bindValues[$k] = $v;
                    }
                }
            }
            if ($bindValues)
            {
                ksort($bindValues);
                $bindValues = array_values($bindValues);
            }
            $this->queryResult = $queryResult = $pgDb->execute($this->statementName, $bindValues);
            if (false === $queryResult)
            {
                throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
            }
        }
        $this->result = $pgDb->fetchAll($queryResult, \SW_PGSQL_ASSOC) ?: [];

        return true;
    }

    /**
     * 从结果集中获取下一行.
     *
     * @return mixed
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0)
    {
        $result = current($this->result);
        if ($result)
        {
            next($this->result);
        }

        return $result;
    }

    /**
     * 返回一个包含结果集中所有行的数组.
     *
     * @param mixed $fetchArgument
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = []): array
    {
        return $this->result;
    }

    /**
     * 从结果集中的下一行返回单独的一列，不存在返回null.
     *
     * @param int|string $columnKey
     *
     * @return mixed
     */
    public function fetchColumn($columnKey = 0)
    {
        $row = current($this->result);
        if ($row)
        {
            next($this->result);
            if (isset($row[$columnKey]))
            {
                return $row[$columnKey];
            }
            elseif (is_numeric($columnKey))
            {
                return array_values($row)[$columnKey] ?? null;
            }
        }

        return null;
    }

    /**
     * 获取下一行并作为一个对象返回。
     *
     * @return mixed
     */
    public function fetchObject(string $className = 'stdClass', ?array $ctorArgs = null)
    {
        $row = current($this->result);
        if (false === $row)
        {
            return null;
        }
        next($this->result);
        if ('stdClass' === $className)
        {
            return (object) $row;
        }
        $result = new $className();
        foreach ($row as $k => $v)
        {
            $result->$k = $v;
        }

        return $result;
    }

    /**
     * 检索一个语句属性.
     *
     * @param mixed $attribute
     *
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return null;
    }

    /**
     * 设置属性.
     *
     * @param mixed $attribute
     * @param mixed $value
     */
    public function setAttribute($attribute, $value): bool
    {
        return true;
    }

    /**
     * 在一个多行集语句句柄中推进到下一个行集.
     */
    public function nextRowset(): bool
    {
        return false;
    }

    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->db->lastInsertId($name ?? $this->statementName);
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int
    {
        return $this->db->rowCount();
    }

    /**
     * 获取原对象实例.
     *
     * @return object
     */
    public function getInstance()
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->result);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->result);
    }

    public function next(): void
    {
        next($this->result);
    }

    public function rewind(): void
    {
        reset($this->result);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return false !== $this->current();
    }
}
