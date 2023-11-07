<?php

declare(strict_types=1);

namespace Imi\Swoole\Db\Driver\Swoole;

use Imi\Db\Exception\DbException;
use Imi\Db\Mysql\Contract\IMysqlDb;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBaseStatement;

/**
 * Swoole Coroutine MySQL 驱动 Statement.
 */
class Statement extends MysqlBaseStatement implements IMysqlStatement
{
    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * 绑定数据.
     */
    protected array $bindValues = [];

    /**
     * 结果数组.
     */
    protected array $result = [];

    public function __construct(
        /**
         * 数据库操作对象
         */
        protected ?IMysqlDb $db,
        /**
         * Statement.
         */
        protected \Swoole\Coroutine\MySQL\Statement|array|bool $statement,
        /**
         * 最后执行过的SQL语句.
         */
        protected string $lastSql,
        /**
         * SQL 参数映射.
         */
        protected ?array $sqlParamsMap = null)
    {
        if (\is_array($statement))
        {
            $this->result = $statement;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDb(): IMysqlDb
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
    public function bindParam(string|int $param, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        $this->bindValues[$param] = $var;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue(string|int $param, mixed $value, int $type = \PDO::PARAM_STR): bool
    {
        $this->bindValues[$param] = $value;

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
        return \is_array($this->statement) ? $this->db->errorCode() : $this->statement->errno;
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo(): string
    {
        return \is_array($this->statement) ? $this->db->errorInfo() : $this->statement->error;
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
        $statement = $this->statement;
        if (\is_array($statement))
        {
            $result = $this->db->getInstance()->query($this->lastSql);
            if (false === $result)
            {
                $db = $this->db;
                $dbInstance = $db->getInstance();
                $errorCode = $dbInstance->errorCode();
                $errorInfo = $dbInstance->errorInfo();
                if ($db->checkCodeIsOffline($errorCode))
                {
                    $db->close();
                }
                throw new DbException('SQL query error: [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $this->getSql() . \PHP_EOL);
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
                        else
                        {
                            // for inputParameters paramName : null
                            $bindValues[$index] = null;
                        }
                    }
                }
                else
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
            $result = $statement->execute($bindValues);
            if (true === $result)
            {
                $result = $statement->fetchAll();
                if (false === $result)
                {
                    $result = [];
                }
            }
            elseif (false === $result)
            {
                $errorCode = $this->errorCode();
                $errorInfo = $this->errorInfo();
                if ($this->db->checkCodeIsOffline($errorCode))
                {
                    $this->db->close();
                }
                throw new DbException('SQL query error: [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $this->getSql() . \PHP_EOL);
            }
        }
        $this->result = (true === $result ? [] : $result);

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
        if ($this->statement instanceof \Swoole\Coroutine\MySQL\Statement)
        {
            $result = $this->statement->nextResult();
            if ($result)
            {
                $result = $this->statement->fetchAll();
                if (false === $result)
                {
                    $result = [];
                }
                $this->result = $result;

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(?string $name = null): string
    {
        return \is_array($this->statement) ? $this->db->lastInsertId() : (string) $this->statement->insert_id;
    }

    /**
     * {@inheritDoc}
     */
    public function rowCount(): int
    {
        return \is_array($this->statement) ? $this->db->rowCount() : $this->statement->affected_rows;
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): object
    {
        return $this->statement;
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
}
