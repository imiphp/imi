<?php

declare(strict_types=1);

namespace Imi\Db\Drivers;

use Imi\Db\Exception\DbException;
use Imi\Db\Interfaces\IDb;
use Imi\Util\Text;

trait TPdoStatement
{
    /**
     * \PDOStatement.
     */
    protected ?\PDOStatement $statement = null;

    /**
     * 数据.
     */
    protected array $data = [];

    /**
     * 数据库操作对象
     */
    protected ?IDb $db = null;

    /**
     * 最后插入ID.
     *
     * @var int|string
     */
    protected $lastInsertId = '';

    public function __construct(IDb $db, \PDOStatement $statement, bool $isExecuted = false)
    {
        $this->db = $db;
        $this->statement = $statement;
        if ($isExecuted)
        {
            $this->updateLastInsertId();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDb(): IDb
    {
        return $this->db;
    }

    /**
     * {@inheritDoc}
     */
    public function bindColumn(string|int $column, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        return $this->statement->bindColumn($column, $param, $type, $maxLength, $driverOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function bindParam(string|int $param, mixed &$var, int $type = \PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
        return $this->statement->bindParam($param, $variable, $type, $maxLength, $driverOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue(string|int $param, mixed $value, int $type = \PDO::PARAM_STR): bool
    {
        return $this->statement->bindValue($param, $value, $type);
    }

    /**
     * {@inheritDoc}
     */
    public function closeCursor(): bool
    {
        return $this->statement->closeCursor();
    }

    /**
     * {@inheritDoc}
     */
    public function columnCount(): int
    {
        return $this->statement->columnCount();
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode(): mixed
    {
        return $this->statement->errorCode();
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getSql(): string
    {
        return $this->statement->queryString;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $inputParameters = null): bool
    {
        try
        {
            $statement = $this->statement;
            $statement->closeCursor();
            if ($inputParameters)
            {
                foreach ($inputParameters as $k => $v)
                {
                    if (\is_int($k))
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
                $errorCode = $this->errorCode();
                $errorInfo = $this->errorInfo();
                if ($this->db->checkCodeIsOffline($errorCode))
                {
                    $this->db->close();
                }
                throw new DbException('SQL query error [' . $errorCode . '] ' . $errorInfo . \PHP_EOL . 'sql: ' . $this->getSql() . \PHP_EOL);
            }
            $this->updateLastInsertId();
        }
        catch (\PDOException $e)
        {
            if (isset($e->errorInfo[0]) && $this->db->checkCodeIsOffline($e->errorInfo[0]))
            {
                $this->db->close();
            }
            throw $e;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        return $this->statement->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAll(int $fetchStyle = \PDO::FETCH_ASSOC, mixed $fetchArgument = null, array $ctorArgs = []): array
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
     * {@inheritDoc}
     */
    public function fetchColumn(int $column = 0): mixed
    {
        return $this->statement->fetchColumn($column);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchObject(string $className = \stdClass::class, ?array $ctorArgs = null): mixed
    {
        return $this->statement->fetchObject($className, $ctorArgs);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute(mixed $attribute): mixed
    {
        return $this->statement->getAttribute($attribute);
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute(mixed $attribute, mixed $value): bool
    {
        return $this->statement->setAttribute($attribute, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function nextRowset(): bool
    {
        return $this->statement->nextRowset();
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId(?string $name = null): string
    {
        if (null === $name)
        {
            return (string) $this->lastInsertId;
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
        return $this->statement->rowCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstance(): object
    {
        return $this->statement;
    }

    /**
     * @return mixed|false
     */
    public function current(): mixed
    {
        // @phpstan-ignore-next-line
        return current($this->statement);
    }

    public function key(): int|string|null
    {
        // @phpstan-ignore-next-line
        return key($this->statement);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        // @phpstan-ignore-next-line
        next($this->statement);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        // @phpstan-ignore-next-line
        reset($this->statement);
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return false !== $this->current();
    }

    /**
     * 根据值类型获取PDO数据类型.
     */
    protected function getDataTypeByValue(mixed $value): int
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
        if (\is_resource($value))
        {
            return \PDO::PARAM_LOB;
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
