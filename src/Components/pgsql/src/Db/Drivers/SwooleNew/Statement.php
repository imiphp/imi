<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\SwooleNew;

use Imi\Db\Exception\DbException;
use Imi\Pgsql\Db\Contract\IPgsqlDb;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBaseStatement;
use Imi\Swoole\Util\Coroutine;
use Swoole\Coroutine\PostgreSQLStatement;

/**
 * Swoole Coroutine Pgsql 驱动 Statement.
 */
class Statement extends PgsqlBaseStatement implements IPgsqlStatement
{
    protected PostgreSQLStatement $stmt;

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

    public function __construct(IPgsqlDb $db, PostgreSQLStatement $stmt, string $originSql, ?array $sqlParamsMap = null)
    {
        $this->db = $db;
        $this->stmt = $stmt;
        $this->lastSql = $originSql;
        $this->sqlParamsMap = $sqlParamsMap;
        if ($result = $stmt->fetchAll(\SW_PGSQL_ASSOC))
        {
            $this->result = $result;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDb(): IPgsqlDb
    {
        return $this->db;
    }

    /**
     * {@inheritDoc}
     */
    public function bindColumn($column, &$param, ?int $type = null, ?int $maxLen = null, $driverData = null): bool
    {
        $this->bindValues[$column] = $param;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindParam($parameter, &$variable, int $dataType = \PDO::PARAM_STR, ?int $length = null, $driverOptions = null): bool
    {
        $this->bindValues[$parameter] = $variable;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool
    {
        if (\is_int($parameter))
        {
            --$parameter;
        }
        $this->bindValues[$parameter] = $value;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function closeCursor(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function columnCount(): int
    {
        return \count($this->result[0] ?? []);
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode()
    {
        if ($this->stmt->resultDiag)
        {
            return $this->stmt->resultDiag['sqlstate'] ?? null;
        }
        else
        {
            return '';
        }
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo(): string
    {
        return $this->stmt->error ?? '';
    }

    /**
     * {@inheritDoc}
     */
    public function getSql(): string
    {
        return $this->lastSql;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $inputParameters = null): bool
    {
        if (null === $inputParameters)
        {
            $inputParameters = $this->bindValues;
        }
        $this->bindValues = $bindValues = [];
        if ($inputParameters)
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
                    elseif (isset($inputParameters[$key = ':' . $paramName]))
                    {
                        $bindValues[$index] = $inputParameters[$key];
                    }
                    elseif (isset($inputParameters[$index]))
                    {
                        $bindValues[$index] = $inputParameters[$index];
                    }
                }
            }
            else
            {
                $bindValues = array_values($inputParameters);
            }
        }
        $stmt = $this->stmt;
        if (false === $stmt->execute($bindValues))
        {
            $errorCode = $this->errorCode();
            $errorInfo = $this->errorInfo();
            if ($this->db->checkCodeIsOffline($errorCode))
            {
                $this->db->close();
            }
            throw new DbException('SQL query error: [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $this->getSql() . \PHP_EOL);
        }
        $this->result = $stmt->fetchAll(\SW_PGSQL_ASSOC) ?: [];

        return true;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, $fetchArgument = null, array $ctorArgs = []): array
    {
        return $this->result;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function fetchObject(string $className = \stdClass::class, ?array $ctorArgs = null)
    {
        $row = current($this->result);
        if (false === $row)
        {
            return null;
        }
        next($this->result);
        if (\stdClass::class === $className)
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
     * {@inheritDoc}
     */
    public function getAttribute($attribute)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($attribute, $value): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function nextRowset(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->stmt->lastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance()
    {
        return $this;
    }

    /**
     * @return mixed|false
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->result);
    }

    /**
     * @return int|string|null
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->result);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        next($this->result);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        reset($this->result);
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return false !== $this->current();
    }
}
