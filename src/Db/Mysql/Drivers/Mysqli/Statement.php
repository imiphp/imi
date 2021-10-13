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
     * {@inheritDoc}
     */
    public function getDb(): IMysqlDb
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
        return $this->result->field_count ?? 0;
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode()
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
            throw new DbException('SQL query error: [' . $this->errorCode() . '] ' . $this->errorInfo() . ' sql: ' . $this->getSql());
        }
        $this->result = $statement->get_result();

        return $result;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function fetchColumn($columnKey = 0)
    {
        $row = $this->result->fetch_array(\MYSQLI_BOTH);

        return $row[$columnKey] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchObject(string $className = 'stdClass', ?array $ctorArgs = null)
    {
        return $this->result->fetch_object();
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
    public function getInstance()
    {
        return $this->statement;
    }

    /**
     * @return mixed|false
     */
    public function current()
    {
        throw new DbException('Not support current()');
    }

    /**
     * @return int|string|null
     */
    public function key()
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
