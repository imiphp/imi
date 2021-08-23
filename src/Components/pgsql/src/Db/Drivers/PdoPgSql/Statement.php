<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\PdoPgSql;

use Imi\Db\Exception\DbException;
use Imi\Pgsql\Db\Contract\IPgsqlDb;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBaseStatement;
use Imi\Util\Text;
use PDOStatement;

/**
 * PDO Pgsql驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends PgsqlBaseStatement implements IPgsqlStatement
{
    /**
     * \PDOStatement.
     */
    protected PDOStatement $statement;

    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * 数据库操作对象
     */
    protected IPgsqlDb $db;

    /**
     * 最后插入ID.
     */
    protected int $lastInsertId = 0;

    public function __construct(IPgsqlDb $db, PDOStatement $statement)
    {
        $this->db = $db;
        $this->statement = $statement;
        $this->updateLastInsertId();
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
        return $this->statement->bindColumn($column, $param, $type, $maxLen, $driverData);
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
        return $this->statement->bindParam($parameter, $variable, $dataType, $length, $driverOptions);
    }

    /**
     * 把一个值绑定到一个参数.
     *
     * @param mixed $parameter
     * @param mixed $value
     */
    public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool
    {
        return $this->statement->bindValue($parameter, $value, $dataType);
    }

    /**
     * 关闭游标，使语句能再次被执行。
     */
    public function closeCursor(): bool
    {
        return $this->statement->closeCursor();
    }

    /**
     * 返回结果集中的列数.
     */
    public function columnCount(): int
    {
        return $this->statement->columnCount();
    }

    /**
     * 返回错误码
     *
     * @return mixed
     */
    public function errorCode()
    {
        return $this->statement->errorCode();
    }

    /**
     * 返回错误信息.
     */
    public function errorInfo(): string
    {
        $errorInfo = $this->statement->errorInfo();
        if (null === $errorInfo[1] && null === $errorInfo[2])
        {
            return '';
        }

        return $errorInfo[1] . ':' . $errorInfo[2];
    }

    /**
     * 获取SQL语句.
     */
    public function getSql(): string
    {
        return $this->statement->queryString;
    }

    /**
     * 执行一条预处理语句.
     *
     * @param array $inputParameters
     */
    public function execute(array $inputParameters = null): bool
    {
        $statement = $this->statement;
        $statement->closeCursor();
        if ($inputParameters)
        {
            foreach ($inputParameters as $k => $v)
            {
                if (is_numeric($k))
                {
                    $statement->bindValue($k + 1, $v, $this->getDataTypeByValue($v));
                }
                else
                {
                    $statement->bindValue($k, $v, $this->getDataTypeByValue($v));
                }
            }
        }
        $result = $statement->execute();
        if (!$result)
        {
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
        }
        $this->updateLastInsertId();

        return $result;
    }

    /**
     * 从结果集中获取下一行.
     *
     * @return mixed
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0)
    {
        return $this->statement->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * 返回一个包含结果集中所有行的数组.
     *
     * @param mixed $fetchArgument
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = []): array
    {
        if (null === $fetchArgument)
        {
            return $this->statement->fetchAll($fetchStyle);
        }
        elseif ([] === $ctorArgs)
        {
            return $this->statement->fetchAll($fetchStyle, $fetchArgument);
        }
        else
        {
            return $this->statement->fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
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
        return $this->statement->fetchColumn($columnKey);
    }

    /**
     * 获取下一行并作为一个对象返回。
     *
     * @return mixed
     */
    public function fetchObject(string $className = 'stdClass', ?array $ctorArgs = null)
    {
        return $this->statement->fetchObject($className, $ctorArgs);
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
        return $this->statement->getAttribute($attribute);
    }

    /**
     * 设置属性.
     *
     * @param mixed $attribute
     * @param mixed $value
     */
    public function setAttribute($attribute, $value): bool
    {
        return $this->statement->setAttribute($attribute, $value);
    }

    /**
     * 在一个多行集语句句柄中推进到下一个行集.
     */
    public function nextRowset(): bool
    {
        return $this->statement->nextRowset();
    }

    /**
     * 返回最后插入行的ID或序列值
     */
    public function lastInsertId(?string $name = null): string
    {
        return (string) $this->lastInsertId;
    }

    /**
     * 返回受上一个 SQL 语句影响的行数.
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
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
        // @phpstan-ignore-next-line
        return current($this->statement);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        // @phpstan-ignore-next-line
        return key($this->statement);
    }

    public function next(): void
    {
        // @phpstan-ignore-next-line
        next($this->statement);
    }

    public function rewind(): void
    {
        // @phpstan-ignore-next-line
        reset($this->statement);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return false !== $this->current();
    }

    /**
     * 根据值类型获取PDO数据类型.
     *
     * @param mixed $value
     */
    protected function getDataTypeByValue($value): int
    {
        if (null === $value)
        {
            return \PDO::PARAM_NULL;
        }
        if (\is_bool($value))
        {
            return \PDO::PARAM_BOOL;
        }
        if (\is_int($value))
        {
            return \PDO::PARAM_INT;
        }

        return \PDO::PARAM_STR;
    }

    /**
     * 更新最后插入ID.
     */
    private function updateLastInsertId(): void
    {
        $queryString = $this->statement->queryString;
        if (Text::startwith($queryString, 'insert ', false) || Text::startwith($queryString, 'replace ', false))
        {
            $this->lastInsertId = (int) $this->db->lastInsertId();
        }
        else
        {
            $this->lastInsertId = 0;
        }
    }
}
