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
     * Statement.
     *
     * @var \Swoole\Coroutine\MySQL\Statement|array
     */
    protected $statement;

    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * 数据库操作对象
     */
    protected ?IMysqlDb $db = null;

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
     * @param \Swoole\Coroutine\MySQL\Statement|array $statement
     */
    public function __construct(IMysqlDb $db, $statement, string $originSql, ?array $sqlParamsMap = null)
    {
        $this->db = $db;
        $this->statement = $statement;
        if (\is_array($statement))
        {
            $this->result = $statement;
        }
        $this->lastSql = $originSql;
        $this->sqlParamsMap = $sqlParamsMap;
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
    public function bindColumn($column, &$param, ?int $type = null, ?int $maxLen = 0, $driverData = null): bool
    {
        $this->bindValues[$column] = $param;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindParam($parameter, &$variable, int $dataType = \PDO::PARAM_STR, ?int $length = 0, $driverOptions = null): bool
    {
        $this->bindValues[$parameter] = $variable;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool
    {
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
    public function getInstance()
    {
        return $this->statement;
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
