<?php

declare(strict_types=1);

namespace Imi\Pgsql\Db\Drivers\SwooleNew;

use Imi\Db\Exception\DbException;
use Imi\Pgsql\Db\Contract\IPgsqlDb;
use Imi\Pgsql\Db\Contract\IPgsqlStatement;
use Imi\Pgsql\Db\PgsqlBaseStatement;
use Imi\Swoole\Util\Coroutine;
use Imi\Util\Text;
use Swoole\Coroutine\PostgreSQLStatement;

/**
 * Swoole Coroutine Pgsql 驱动 Statement.
 */
class Statement extends PgsqlBaseStatement implements IPgsqlStatement
{
    /**
     * 绑定数据.
     */
    protected array $bindValues = [];

    /**
     * 结果数组.
     */
    protected array $result = [];

    /**
     * 最后插入ID.
     */
    protected string $lastInsertId = '';

    public function __construct(
        /**
         * 数据库操作对象
         */
        protected ?IPgsqlDb $db, protected ?PostgreSQLStatement $stmt,
        /**
         * 最后执行过的SQL语句.
         */
        protected string $lastSql,
        /**
         * SQL 参数映射.
         */
        protected ?array $sqlParamsMap = null, bool $isExecuted = false)
    {
        if ($isExecuted)
        {
            if ($result = $stmt->fetchAll(\SW_PGSQL_ASSOC))
            {
                $this->result = $result;
            }
            $this->updateLastInsertId();
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
    public function bindColumn(string|int $column, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        $this->bindValues[$column] = $var;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        $this->bindValues[$param] = $var;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue(string|int $param, mixed $value, int $type = \PDO::PARAM_STR): bool
    {
        if (\is_int($param))
        {
            --$param;
        }
        $this->bindValues[$param] = $this->parseValue($value);

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
    public function errorCode(): mixed
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
                        $bindValues[$index] = $this->parseValue($inputParameters[$paramName]);
                    }
                    elseif (isset($inputParameters[$key = ':' . $paramName]))
                    {
                        $bindValues[$index] = $this->parseValue($inputParameters[$key]);
                    }
                    elseif (isset($inputParameters[$index]))
                    {
                        $bindValues[$index] = $this->parseValue($inputParameters[$index]);
                    }
                }
            }
            else
            {
                foreach ($inputParameters as $value)
                {
                    $bindValues[] = $this->parseValue($value);
                }
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
        $this->updateLastInsertId();
        $this->result = $stmt->fetchAll(\SW_PGSQL_ASSOC) ?: [];

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
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
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, mixed $fetchArgument = null, array $ctorArgs = []): array
    {
        return $this->result;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchColumn(int $column = 0): mixed
    {
        $row = current($this->result);
        if ($row)
        {
            next($this->result);
            if (isset($row[$column]))
            {
                return $row[$column];
            }
            elseif (is_numeric($column))
            {
                return array_values($row)[$column] ?? null;
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchObject(string $className = \stdClass::class, ?array $ctorArgs = null): mixed
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
            $result->{$k} = $v;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute(mixed $attribute): mixed
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute(mixed $attribute, mixed $value): bool
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
        if (null === $name)
        {
            return $this->lastInsertId;
        }
        else
        {
            return $this->db->lastInsertId($name);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function rowCount(): int
    {
        return $this->stmt->affectedRows();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): object
    {
        return $this;
    }

    public function current(): mixed
    {
        return current($this->result);
    }

    public function key(): int|string|null
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

    protected function parseValue(mixed $value): mixed
    {
        if (\is_bool($value))
        {
            return (int) $value;
        }

        return $value;
    }

    /**
     * 更新最后插入ID.
     */
    private function updateLastInsertId(): void
    {
        $queryString = $this->lastSql;
        if (Text::startwith($queryString, 'insert ', false) || Text::startwith($queryString, 'replace ', false))
        {
            try
            {
                $this->lastInsertId = $this->db->lastInsertId();
            }
            catch (\Throwable $th)
            {
                if (!str_contains($th->getMessage(), 'lastval is not yet defined in this session'))
                {
                    throw $th;
                }
            }
        }
        else
        {
            $this->lastInsertId = '';
        }
    }
}
