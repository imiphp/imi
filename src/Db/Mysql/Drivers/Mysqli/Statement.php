<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\Mysqli;

use Imi\Db\Exception\DbException;
use Imi\Db\Mysql\Contract\IMysqlDb;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBaseStatement;
use mysqli_result;
use mysqli_stmt;

/**
 * mysqli驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends MysqlBaseStatement implements IMysqlStatement
{
    protected ?mysqli_stmt $statement;

    /**
     * @var \mysqli_result|false
     */
    protected $result;

    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * 数据库操作对象
     */
    protected IMysqlDb $db;

    /**
     * 最后执行过的SQL语句.
     */
    protected string $lastSql = '';

    /**
     * 绑定数据.
     */
    protected array $bindValues = [];

    /**
     * SQL 参数映射.
     */
    protected ?array $sqlParamsMap = null;

    public function __construct(IMysqlDb $db, ?mysqli_stmt $statement, ?mysqli_result $result, string $originSql, ?array $sqlParamsMap = null)
    {
        $this->db = $db;
        $this->statement = $statement;
        $this->result = $result;
        $this->lastSql = $originSql;
        $this->sqlParamsMap = $sqlParamsMap;
    }

    /**
     * 获取数据库操作对象
     */
    public function getDb(): IMysqlDb
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
        return $this->result->field_count ?? 0;
    }

    /**
     * 返回错误码
     *
     * @return mixed
     */
    public function errorCode()
    {
        return $this->statement->errno ?? $this->db->errorCode();
    }

    /**
     * 返回错误信息.
     */
    public function errorInfo(): string
    {
        return $this->statement->error ?? $this->db->errorInfo();
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
        $statement = $this->statement;
        $bindValues = $this->bindValues;
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
            $statement->bind_param($this->getBindTypes($bindValues), ...$bindValues);
        }

        $result = $statement->execute();
        if (!$result)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
        }
        $this->result = $statement->get_result();

        return $result;
    }

    /**
     * 从结果集中获取下一行.
     *
     * @return mixed
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0)
    {
        $result = $this->result;
        switch ($fetchStyle)
        {
            case \PDO::FETCH_ASSOC:
                return $result->fetch_assoc();
            case \PDO::FETCH_BOTH:
                return $result->fetch_array();
            case \PDO::FETCH_NUM:
                return $result->fetch_array(\MYSQLI_NUM);
            case \PDO::FETCH_OBJ:
                return $result->fetch_object();
            default:
                throw new DbException(sprintf('Not support fetchStyle %s', $fetchStyle));
        }
    }

    /**
     * 返回一个包含结果集中所有行的数组.
     *
     * @param mixed $fetchArgument
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = []): array
    {
        $result = $this->result;
        switch ($fetchStyle)
        {
            case \PDO::FETCH_ASSOC:
                return $result->fetch_all(\MYSQLI_ASSOC);
            case \PDO::FETCH_BOTH:
                return $result->fetch_all(\MYSQLI_BOTH);
            case \PDO::FETCH_NUM:
                return $result->fetch_all(\MYSQLI_NUM);
            case \PDO::FETCH_OBJ:
                $return = [];
                foreach ($result->fetch_all(\MYSQLI_ASSOC) as $item)
                {
                    $return[] = (object) $item;
                }

                return $return;
            default:
                throw new DbException(sprintf('Not support fetchStyle %s', $fetchStyle));
        }
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
        $row = $this->result->fetch_array(\MYSQLI_BOTH);

        return $row[$columnKey] ?? null;
    }

    /**
     * 获取下一行并作为一个对象返回。
     *
     * @return mixed
     */
    public function fetchObject(string $className = 'stdClass', ?array $ctorArgs = null)
    {
        return $this->result->fetch_object();
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
        $statement = $this->statement;
        if (!$statement->more_results())
        {
            return false;
        }
        $statement->next_result();
        if ($this->result)
        {
            $this->result->close();
        }
        $this->result = $statement->get_result();

        return true;
    }

    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId(?string $name = null): string
    {
        return (string) ($this->statement->insert_id ?? $this->db->lastInsertId());
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int
    {
        return $this->statement->affected_rows ?? $this->db->rowCount();
    }

    /**
     * 获取原对象实例.
     *
     * @return object
     */
    public function getInstance()
    {
        return $this->statement;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        throw new DbException('Not support current()');
    }

    /**
     * @return mixed
     */
    public function key()
    {
        throw new DbException('Not support key()');
    }

    public function next(): void
    {
        throw new DbException('Not support next()');
    }

    public function rewind(): void
    {
        throw new DbException('Not support rewind()');
    }

    /**
     * @return bool
     */
    public function valid()
    {
        throw new DbException('Not support valid()');
    }

    /**
     * 根据值获取mysqli数据类型.
     */
    protected function getBindTypes(array $values): string
    {
        $types = '';
        foreach ($values as $value)
        {
            if (null === $value)
            {
                $types .= 'b';
            }
            elseif (\is_bool($value) || \is_int($value))
            {
                $types .= 'i';
            }
            elseif (\is_float($value))
            {
                $types .= 'd';
            }
            else
            {
                $types .= 's';
            }
        }

        return $types;
    }
}
