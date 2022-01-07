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
        return $this->db->errorCode();
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo(): string
    {
        return $this->db->errorInfo();
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
        /** @var \Swoole\Coroutine\PostgreSQL $pgDb */
        $pgDb = $this->db->getInstance();
        if (null === $this->statementName)
        {
            $this->queryResult = $queryResult = $pgDb->query($this->lastSql);
            if (false === $queryResult)
            {
                throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql() . \PHP_EOL);
            }
        }
        else
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
            $this->queryResult = $queryResult = $pgDb->execute($this->statementName, $bindValues);
            if (false === $queryResult)
            {
                $errorCode = $this->errorCode();
                $errorInfo = $this->errorInfo();
                if ($this->db->checkCodeIsOffline($errorCode))
                {
                    $this->close();
                }
                throw new DbException('SQL query error: [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $this->getSql() . \PHP_EOL);
            }
        }
        $this->result = $pgDb->fetchAll($queryResult, \SW_PGSQL_ASSOC) ?: [];

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
        return $this->db->lastInsertId($name ?? $this->statementName);
    }

    /**
     * {@inheritDoc}
     */
    public function rowCount(): int
    {
        return $this->db->rowCount();
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
