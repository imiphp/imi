<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\Mysqli;

use Imi\Db\Exception\DbException;
use Imi\Db\Mysql\Contract\IMysqlDb;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBaseStatement;

/**
 * mysqli驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends MysqlBaseStatement implements IMysqlStatement
{
    /**
     * @var \mysqli_result|false
     */
    protected $result;

    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * 绑定数据.
     */
    protected array $bindValues = [];

    public function __construct(
        /**
         * 数据库操作对象
         */
        protected ?IMysqlDb $db, protected ?\mysqli_stmt $statement, ?\mysqli_result $result,
        /**
         * 最后执行过的SQL语句.
         */
        protected string $lastSql,
        /**
         * SQL 参数映射.
         */
        protected ?array $sqlParamsMap = null)
    {
        $this->result = $result;
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
    public function bindColumn(string|int $column, mixed &$var, int $type = 0, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        $this->bindValues[$column] = $var;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindParam(string|int $param, mixed &$var, int $type = 0, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        $this->bindValues[$param] = $var;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue(string|int $param, mixed $value, int $type = 0): bool
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
        return $this->result->field_count ?? 0;
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode(): mixed
    {
        return $this->statement->errno ?? $this->db->errorCode();
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo(): string
    {
        return $this->statement->error ?? $this->db->errorInfo();
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
            $statement->bind_param($this->getBindTypes($bindValues), ...$bindValues);
        }

        $result = $statement->execute();
        if (!$result)
        {
            $errorCode = $this->errorCode();
            $errorInfo = $this->errorInfo();
            if ($this->db->checkCodeIsOffline($errorCode))
            {
                $this->db->close();
            }
            throw new DbException('SQL query error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $this->getSql() . \PHP_EOL);
        }
        $this->result = $statement->get_result();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(int $fetchStyle = FetchType::FETCH_ASSOC, int $cursorOrientation = 0, int $cursorOffset = 0): mixed
    {
        $result = $this->result;

        return match ($fetchStyle)
        {
            FetchType::FETCH_ASSOC => $result->fetch_assoc(),
            FetchType::FETCH_BOTH  => $result->fetch_array(),
            FetchType::FETCH_NUM   => $result->fetch_array(\MYSQLI_NUM),
            FetchType::FETCH_OBJ   => $result->fetch_object(),
            default                => throw new DbException(sprintf('Not support fetchStyle %s', $fetchStyle)),
        };
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAll(int $fetchStyle = FetchType::FETCH_ASSOC, mixed $fetchArgument = null, array $ctorArgs = []): array
    {
        $result = $this->result;
        switch ($fetchStyle)
        {
            case FetchType::FETCH_ASSOC:
                return $result->fetch_all(\MYSQLI_ASSOC);
            case FetchType::FETCH_BOTH:
                return $result->fetch_all(\MYSQLI_BOTH);
            case FetchType::FETCH_NUM:
                return $result->fetch_all(\MYSQLI_NUM);
            case FetchType::FETCH_OBJ:
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
     * {@inheritDoc}
     */
    public function fetchColumn(int $column = 0): mixed
    {
        $row = $this->result->fetch_array(\MYSQLI_BOTH);

        return $row[$column] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchObject(string $className = \stdClass::class, ?array $ctorArgs = null): mixed
    {
        return $this->result->fetch_object();
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
     * {@inheritDoc}
     */
    public function lastInsertId(?string $name = null): string
    {
        return (string) ($this->statement->insert_id ?? $this->db->lastInsertId());
    }

    /**
     * {@inheritDoc}
     */
    public function rowCount(): int
    {
        return $this->statement->affected_rows ?? $this->db->rowCount();
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
        throw new DbException('Not support current()');
    }

    public function key(): int|string|null
    {
        throw new DbException('Not support key()');
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        throw new DbException('Not support next()');
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        throw new DbException('Not support rewind()');
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
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
