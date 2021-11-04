<?php

declare(strict_types=1);

namespace Imi\Db\Mysql\Drivers\PdoMysql;

use Imi\Db\Exception\DbException;
use Imi\Db\Mysql\Contract\IMysqlDb;
use Imi\Db\Mysql\Contract\IMysqlStatement;
use Imi\Db\Mysql\Drivers\MysqlBaseStatement;
use Imi\Util\Text;
use PDOStatement;

/**
 * PDO MySQL驱动Statement.
 *
 * @property string $queryString
 */
class Statement extends MysqlBaseStatement implements IMysqlStatement
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
    protected IMysqlDb $db;

    /**
     * 最后插入ID.
     */
    protected int $lastInsertId = 0;

    public function __construct(IMysqlDb $db, PDOStatement $statement)
    {
        $this->db = $db;
        $this->statement = $statement;
        $this->updateLastInsertId();
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
        return $this->statement->bindColumn($column, $param, $type, $maxLen, $driverData);
    }

    /**
     * {@inheritDoc}
     */
    public function bindParam($parameter, &$variable, int $dataType = \PDO::PARAM_STR, ?int $length = null, $driverOptions = null): bool
    {
        return $this->statement->bindParam($parameter, $variable, $dataType, $length, $driverOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue($parameter, $value, int $dataType = \PDO::PARAM_STR): bool
    {
        return $this->statement->bindValue($parameter, $value, $dataType);
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
    public function errorCode()
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
     * {@inheritDoc}
     */
    public function fetch(int $fetchStyle = \PDO::FETCH_ASSOC, int $cursorOrientation = \PDO::FETCH_ORI_NEXT, int $cursorOffset = 0)
    {
        return $this->statement->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function fetchColumn($columnKey = 0)
    {
        return $this->statement->fetchColumn($columnKey);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchObject(string $className = 'stdClass', ?array $ctorArgs = null)
    {
        return $this->statement->fetchObject($className, $ctorArgs);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($attribute)
    {
        return $this->statement->getAttribute($attribute);
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($attribute, $value): bool
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
        return (string) $this->lastInsertId;
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
        // @phpstan-ignore-next-line
        return current($this->statement);
    }

    /**
     * @return int|string|null
     */
    #[\ReturnTypeWillChange]
    public function key()
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
    #[\ReturnTypeWillChange]
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
